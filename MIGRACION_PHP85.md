# Migración del ACD a PHP 8.5

Rama `feature/integracion_php85`, solo en local: no está subida, no tiene PR ni tag.

## Objetivo

- Que el ACD (`movistarplus/acd`) funcione en **PHP 8.5.12 en contenedores**, con **MongoDB 7, 8 o 9** en producción. La versión de MongoDB está por decidir.
- Que funcione **igual que la versión de PHP 7**. No tiene que seguir siendo compatible con PHP 7.
- Que incorpore las funcionalidades del ACD de neo (`movistarplustad/neo`, `editores/acd`).
- Que corrija los errores detectados.
- Consumidores de esta librería, vía Composer: **avatar** y **redirecciones**. Neo y legacy usan su propia copia del ACD.

## Entorno

| Pieza | Versión |
|---|---|
| Imagen PHP | `registry-cicd.apps.ocpmovistar001.interactivos.int/bob/fau/config/movistarbaseimage:php8.5.12` (Apache, ext-mongodb 2.5.5, `variables_order = GPCS`) |
| `mongodb/mongodb` | ^2.0 (lock: 2.3.0) |
| `vlucas/phpdotenv` | ^5.5.9 (lock: 5.7.0) |
| `php` (composer.json) | ^8.3 |
| MongoDB probado | 9.0 y 7.0.45 |

- **MongoDB 8:** sin probar. La 8.0.21 no arranca en hosts con kernel 6.19 o posterior (SERVER-121912).
- **MongoDB 3.6:** no compatible. ext-mongodb 2.x exige MongoDB 4.2 o superior.
- **Imagen sin composer:** las dependencias se instalan con `composer install --ignore-platform-reqs`.

## Historia de la rama

La rama parte de `feature/php85` e incluye dos merges:
- **`origin/master`** (`81b6888`).
- **`v1.0.10`** (`d470d39`): `feature/23035_upgrade_architecture_2024` hasta lo que llegó a producción, sin FlySystem. La rama de FlySystem se conserva sin tocar.

En el conflicto de `composer.json` se mantuvieron las versiones de php85.

### Base heredada de `feature/php85`

| Commit | Cambio |
|---|---|
| `a56ebb4` | El aplicativo arranca con PHP 8.3 (Dockerfile, docker-compose, dependencias) |
| `31d4478` | Corregida la clave de la cookie de preidentificación (`ACD_COOKIE_PREFIX `, con espacio) |
| `8f293a6` | Preparación PHP 8 (`Auth`, `composer.json`) |
| `c7bdfe0` | Actualización de dependencias, phpdotenv 5 |
| `d5fa7f4` | `mongodb/mongodb` ^2.0 y `count()` sustituido por `countDocuments()` |
| `24bae2e` | phpdotenv 5 con `createUnsafeImmutable`, para que `getenv()` siga funcionando |

### Correcciones de claves y carga

| Commit | Cambio |
|---|---|
| `dad66a2` | `ContentLoader`: clave `ACD_DATA_CONTENT_BINARY_ORIGIN_FORM_PATH` corregida |
| `832c628` | `PersistentUserManagerTextPlain`: clave `ACD_PATH_AUTH_PERMANENT_LOGIN_DIR` corregida |
| `6adc4f8` | `FieldDo::load` acepta arrays (text/plain) y BSONDocument (MongoDB). El arreglo de master rompía las estructuras cargadas desde un array |

### Funcionalidades portadas del ACD de neo

| Commit | Cambio | Origen en neo |
|---|---|---|
| `31146b3` | CKEditor: botón de tachado | d73af4e57c, NEODES-231 |
| `221e689` | Nueva consulta `shortlink-search` y `Filter::periodOfValidityRange` para el acortador de enlaces | NEODES-459 y NEODES-512 |
| `7a29800` | Búsqueda del editor por los campos `all`, `title`, `alias_id` y `tags`, con el texto escapado (`preg_quote`) | NEODES-504 y NEODES-540 |
| `46fbca4` | tag-deep: filtra la vigencia en MongoDB, admite tags opcionales, omite contenidos no válidos y ordena por `data.titulo` descendente | NEODES-673 |
| `a0fcf94` | Condición `order` (asc/desc por `save_ts`) en tag-deep | NEODES-702 |
| `13e53c6` | Listado `all`: el rol editor no ve los contenidos con tag `blog` | NEODES-757 |
| `d61a4db` | Un contenido inexistente devuelve `null` en vez de un contenido vacío; borrarlo da 404 | NEODES-618 (rama sin mergear en neo) |

### Compatibilidad con PHP 8.x

| Commit | Cambio |
|---|---|
| `c2407c8` | Añadida `ContentLoaderException` y los `use` de excepciones que faltaban tras la migración a PSR-4 |
| `9bbf337` | `Field` y `Template` lanzan `\Exception` (la clase `Acd\View\Exception` no existía) |
| `f8c75b7` | `PersistentStructureManagerMongoDBException` y `PersistentManagerTextPlainException` están en su propio fichero, porque estaban declaradas dos veces |
| `0226788` | `Auth::getRol()` devuelve `null` si no hay sesión |
| `a94d2c2` | Casts canónicos `(bool)`, `(int)` y `(float)`, en lugar de `(boolean)`, `(integer)` y `(double)` |
| `d3025d2` | Propiedades declaradas en lugar de dinámicas. `Limits` usa `$step`; `Relation` y `Summary` usan `$idContent` |
| `da90d36` | `Collection::getIterator(): \Traversable` |
| `366bb51` | `ContentDo::$data` y `FieldDo::$dirtyNumItem`, declaradas al final de la clase (ver «Contrato con redirecciones») |

### Configuración, contratos y editor

| Commit | Cambio |
|---|---|
| `5ce5629` | Configuración: primero las variables de entorno del contenedor, después `.env.$APPLICATION_ENV` y después `.env`. `.env.example` usa nombres con guion bajo |
| `3a98706` | `validity-date`, `validity-from` y `validity-to` tienen que ser timestamps Unix |
| `661b5dd` | `shortlink-search` escapa el texto `q` |
| `5047ea9` | El listado y las relaciones toleran `null`; `relation.php` da 404 si el contenido no existe |
| `175ce41` | Editor sin avisos de PHP 8.5 con valores vacíos (plantillas, `ValueFormater`, `back.php`) |
| `57b342e` | Parámetros de la petición con tipo (`Acd\Lib\Request`): un array donde se espera texto (`s[]=x`) se trata como ausente en vez de acabar en TypeError (500). En los `do_*`, el `id` ausente es `''`, así que el alta de estructura ya no puede guardar una estructura sin id. Sin aviso de PHP 8 al dar de alta usuarios y enumerados |
| `da5946a` | Pruebas: suite Playwright y scripts de comparación |

## Cambios que afectan a los consumidores

1. **Fechas de vigencia en timestamp.**
   - `Query::setCondition` lanza `InvalidArgumentException` si `validity-date`, `validity-from` o `validity-to` no son un entero.
   - Avatar ya pasa `time()`.
   - El blog de neo pasa `date('Y-m-d')`, pero usa su propia copia del ACD. Habrá que cambiarlo cuando neo use esta.
2. **Contenido inexistente.**
   - `loadContent('id')`, `id-deep` e `id+countParents` devuelven `null`. Antes devolvían un `ContentDo` vacío, con avisos de PHP.
   - `loadContents()` devuelve `null` cuando no hay resultado. Antes lanzaba `ContentLoaderException`.
   - `deleteContent()` de un id inexistente lanza `ContentLoaderException` con código 404.
3. **Configuración.**
   - Las variables del contenedor tienen prioridad sobre `.env`.
   - Los nombres con punto (`ACD_STORAGE_TYPE.MONGODB.NAME`) siguen aceptándose, pero los nombres con guion bajo tienen prioridad. El entrypoint `/bin/sh` de la imagen descarta las variables con punto.
   - `ACD_ROL_EDITOR` tiene que estar definida en el consumidor. Si no lo está, el listado `all` aplica el filtro de blog.
4. **Búsqueda del editor (`editor-search`).** Ahora busca de verdad por `alias_id`, `title` y `tags`. Antes, `alias_id` se ignoraba y se devolvían los 100 primeros contenidos. Redirecciones lo usa en el borrado, que compara de forma estricta, así que es una mejora.
5. **tag-deep.**
   - Ordena siempre por `data.titulo` descendente, salvo que se pase `order`.
   - Filtra la vigencia en MongoDB.
   - Es el comportamiento de neo, que en master no estaba (ver «Riesgos»).

### Contrato con redirecciones

Redirecciones lee `ContentDo` y `FieldDo` convertidos a array **por posición**:
- `DownloadHandler.php:15`: en `ContentDo`, el índice 8 son los campos.
- `SearchHandler.php:98`: en `FieldDo`, el índice 3 es el valor.

Al declarar las propiedades que antes eran dinámicas, hay que ponerlas **al final** de la clase, para mantener esas posiciones. `tests/migracion-php85/redirecciones.php` lo comprueba.

## Riesgos y pendientes

| # | Asunto | Gravedad | Estado |
|---|---|---|---|
| 1 | **Composer de redirecciones.** Redirecciones vive en `webplus/editores` con Laravel 7, que exige phpdotenv ^4. Esta versión del ACD exige `php ^8.3` y phpdotenv ^5, así que no se puede instalar ahí tal como está. Redirecciones se va a migrar a PHP 8.5.12, y esa migración tiene que incluir un Laravel con phpdotenv 5. Avatar (Laravel 13, phpdotenv 5.7) sí es compatible | Alta para el despliegue | Decidido: se resuelve con la migración de redirecciones a PHP 8.5.12 (fuera de este repo). Hasta entonces, no desplegar esta versión en redirecciones |
| 2 | **tag-deep ordena siempre por `data.titulo` -1.** Igual que en neo, pero en master no se ordenaba, así que `loadContent('tag-deep')` puede devolver otro contenido. Con títulos repetidos, o en estructuras sin `data.titulo` (como `pagina`), el orden quedaba al azar del motor. No se ha visto que avatar ni redirecciones usen tag-deep | Baja | Decidido: igual que neo, para mantener los dos ACD sincronizados. Se probó `_id` como desempate (`5d4d06b`) y se revirtió (`ffd761b`); queda como mejora a hacer a la vez aquí y en neo. En neo solo lo usa el blog, que ordena por `save_ts` (`order=desc` por defecto) |
| 3 | **tag-deep filtra la vigencia en MongoDB.** Cuando se pasa `validity-date`, solo `''` cuenta como sin límite: los documentos sin `period_of_validity`, con `start`/`end` ausentes o `null`, o con `[]` quedan fuera, mientras que en PHP (`id-deep` y relacionados) se dan por vigentes. `alias-id-deep` ya lo hacía en master. En los datos reales hay 1.587 de 70.469 contenidos en ese caso: 1.141 de 2015 sin campo y sin tags, 440 de `meta-tags`, `blog_modulo_libre` y `post_modulo_base` con `[]` (se cargan como relacionados, con la comprobación de PHP), 4 `blog_post` caducados en 1970 (excluidos con los dos métodos) y 2 `base_legal` de 2011 con tag `migracion`. Ninguna llamada actual de neo cambia de resultado | Baja | Decidido: igual que neo, para mantener los dos ACD sincronizados. La corrección queda en «Mejoras futuras conjuntas con neo» |
| 4 | **`ACD_ROL_EDITOR` en los consumidores.** Si no está definida, o si se comparte sesión con un login de editor del ACD, `all` oculta los contenidos con tag `blog`. `editor-search` no aplica el filtro, igual que en neo | Baja | Definirla en avatar y redirecciones |
| 5 | **Parámetros enviados como array** (`content.php?s[]=x` y similares). Con PHP 8 daban TypeError (500); antes, un aviso. Se revisaron todos los parámetros del editor enviando cada uno como array (142 peticiones) | Baja | Resuelto en `57b342e`. Un array se trata como parámetro ausente; lo que sigue fallando con un valor ausente está en los riesgos 12 y 13 |
| 6 | **MongoDB 8 sin probar** | Media | Probar en un host con kernel compatible |
| 7 | **Convención de variables de entorno** en avatar, redirecciones, neo y legacy | — | Más adelante |
| 8 | **El blog de neo pasa la fecha en texto** | — | Al pasar neo a esta versión |
| 9 | **Tag para avatar y redirecciones** (fijan `v1.1.0`) | — | Cuando se decida |
| 10 | **`content_rel.php` con un padre inexistente** da error | Baja | Ya ocurría en master |
| 11 | **Al clonar no se copian los ficheros.** `file.tpl` es igual en master y neo | — | Comportamiento de siempre |
| 12 | **Estructura o contenido ausente o inexistente en la URL.** `content.php` (`a=new`/`edit` sin `idt` válido), `content_rel.php` (sin `idp`, `idtp`, `idt` o `f` válidos), `relation.php` (sin `id` o `idt` válidos) y `do_process_content.php` (sin `ids`) acaban en una excepción sin capturar (500) en vez de un 404. Solo con URLs manipuladas | Baja | Mejora por hacer: devolver 404. Ya ocurría en master |
| 13 | **Alta de usuario o enumerado con el id vacío.** Desde el formulario normal, dejar el id en blanco da 500 (`Collection::getId()`/`getPassword()`): con id vacío se cargan todos en vez de uno | Baja | Mejora por hacer: validar el id y volver al formulario con error. Ya ocurría en master |
| 14 | **`do_install.php` sin una acción válida.** Si `a` no es «Create DB indexes» ni «Drop DB indexes», `$returnUrl` queda sin definir: aviso de PHP y redirección vacía | Baja | Mejora por hacer: volver a `install.php`. Ya ocurría en master |

### Mejoras futuras conjuntas con neo

No se hacen en esta migración, que busca comportarse igual que el ACD de neo. Hay que hacerlas a la vez aquí y en neo para que los dos sigan sincronizados.

| Mejora | Qué resuelve |
|---|---|
| **Filtro de vigencia con vacíos.** Que `Filter::periodOfValidity` y `Filter::periodOfValidityRange` traten como sin límite el campo ausente, `null` y `[]`, igual que `ContentDo::checkValidityDate` | El riesgo 3: tag-deep y `alias-id-deep` darían el mismo resultado que PHP. Es requisito para llevar a MongoDB el filtro de los relacionados, porque si no desaparecerían los 440 módulos del blog con `[]` |
| **`profile` en MongoDB.** Añadir `Filter::profile` a la consulta de tag-deep | Hoy el perfil se comprueba en PHP después de paginar: con `profile`, las páginas salen incompletas y el total cuenta contenidos de otros perfiles |
| **Agrupar las consultas de los relacionados.** Una consulta `_id` `$in` por campo `collection` (con vigencia y perfil en el filtro), reordenando en PHP según el orden de la colección | Ahora `loadIdDepth` hace un `findOne` por cada relacionado y nivel. Se pasaría de N consultas a una por campo |
| **Desempate por `_id` en tag-deep.** Añadirlo al orden (`data.titulo` o `save_ts`) | Con títulos o fechas repetidos, o en estructuras sin `data.titulo`, el orden lo decide el motor y la paginación no es estable (riesgo 2) |

## Plan de pruebas

### 1. Pruebas automáticas

#### 1.1 Editor web con Playwright (`tests/e2e`)

| Fichero | Qué prueba |
|---|---|
| `01-acceso.spec.ts` | Login, contraseña incorrecta, logout, redirección sin sesión, permisos por rol (el editor recibe 403 en usuarios y enumerados) |
| `02-navegacion.spec.ts` | Con los dos roles, en cada estructura: listado, paginación, alta, búsqueda con `a`, `(test)`, `.*`, `[` y `ñ`, ficha, resumen, clonado, relaciones, alias-id y búsqueda de relaciones |
| `03-contenido.spec.ts` | Con el rol editor: alta (con ñ y acentos), subida de imagen y vista previa (bytes idénticos), relación de dos contenidos desde la búsqueda, padre visible en la página de relaciones del hijo, borrado desactivado si tiene padres, resumen, clonado y borrado de copia y original. Deja la base como estaba |
| `04-no-encontrado.spec.ts` | 404 al editar un id inexistente o no válido, id escapado en el 404, 404 en relaciones y «volver» sin historial |
| `05-parametros.spec.ts` | Parámetros enviados como array: búsqueda del listado y de relaciones con `s[]` (búsqueda vacía), ficha con `idp[]`/`idtp[]`, y alta de estructura con `id[]` (se rechaza y no rompe el listado de estructuras) |

Todas las páginas se comprueban también en busca de errores de PHP en el HTML.

**Cómo se ejecuta:**
1. En la raíz del repo: `composer install --ignore-platform-reqs`.
2. Copia `tests/e2e/.env.sample` a `tests/e2e/.env` y rellena los usuarios, la contraseña y `ACD_E2E_DUMP` (un `mongodump --archive` de la base `acd`).

```bash
tests/e2e/entorno.sh up
```

```bash
tests/e2e/entorno.sh test
```

```bash
tests/e2e/entorno.sh log
```

```bash
tests/e2e/entorno.sh down
```

- `entorno.sh up` levanta MongoDB (por defecto `mongo:9.0`; `ACD_E2E_MONGO_IMAGE=mongo:7.0` para la 7), restaura el volcado y crea los dos usuarios de prueba. También arranca Apache con PHP 8.5.12 en `http://127.0.0.1:18085/`.
- `test` ejecuta la suite en la imagen `movistar-playwright-testing:latest`. `ACD_E2E_MAX_STRUCTURES` limita cuántas estructuras se recorren (por defecto 20).
- `log` muestra el log de errores de PHP del editor, con `error_reporting = E_ALL`.

**Criterio:** todas las pruebas pasan y el log de PHP queda vacío.

#### 1.2 Paridad de consultas con PHP 7 (`tests/migracion-php85/compare.php`)

- **Qué hace:** ejecuta 3.856 consultas de solo lectura con los dos roles: `id`, `id-deep` (con y sin fecha), `id+countParents`, `parents`, `alias-id-deep`, `difuse-alias-id`, `tag-deep`, `tag-one-deep`, `all`, `editor-search` y `shortlink-search`.
- **Muestra:** los 8 primeros contenidos de cada estructura.
- **Referencia:** el mismo script con PHP 7.2 en el contenedor `webplus-webplus-editores-1`, con el ACD de neo y sobre la misma copia de datos.
- **Precaución:** el `.env` base de ese contenedor apunta a producción; hay que forzar siempre el servidor local.
- **Comparación:** con `diff.php referencia.json nuevo.json`.

**Criterio:** solo las 3 diferencias esperadas:
- `shortlink|2` devuelve `null` en vez de una excepción (NEODES-618).
- `shortlink|3` y `shortlink|4` rechazan las fechas en texto (contrato de timestamps).

#### 1.3 Escritura (`tests/migracion-php85/smoke.php`)

Sobre una base desechable (`ACD_MONGODB_DB` tiene que contener «smoke»):
- guarda y carga estructuras;
- crea contenidos con vigencia, borrador y perfil;
- crea relaciones y colecciones;
- edita;
- carga con `id-deep`, `alias-id-deep`, `tag-deep` y perfiles;
- prueba `all`, `editor-search`, `parents` y `countParents`;
- comprueba el borrado: se rechaza si el contenido tiene padres y funciona sin ellos.

**Criterio:** el mismo resultado en MongoDB 7 y 9. El único `[FAIL]` debe ser el rechazo esperado del borrado con padres.

#### 1.4 Contrato con redirecciones (`tests/migracion-php85/redirecciones.php`)

Recorre 300 contenidos reales y comprueba el acceso por posición que hace redirecciones.

**Criterio:** `fallos 0` y código de salida 0.

### 2. Pruebas manuales antes de producción

No están automatizadas. Hay que hacerlas en el entorno de preproducción, con la versión de MongoDB elegida.

- [ ] **Estructuras** (developer): crear, editar campos y restricciones, y borrar una estructura de prueba.
- [ ] **Enumerados y usuarios** (developer): alta, edición y baja.
- [ ] **Login con «Remember»:** cerrar el navegador y volver a entrar sin contraseña (cookie persistente y `authPermanent`).
- [ ] **`install.php`:** listar, crear y borrar índices.
- [ ] **Ficheros grandes y no imagen** (PDF): subida, vista previa y borrado.
- [ ] **CKEditor:** pegar desde Word, tachado, enlaces.
- [ ] **Campos de fecha, rango, color, coordenadas y listas:** guardar y releer el valor.
- [ ] **Avatar con esta versión:**
  - páginas que usan `alias-id-deep` con fecha y perfil;
  - variables `ACD_*`, incluida `ACD_ROL_EDITOR`, recibidas desde el contenedor.
- [ ] **Redirecciones con esta versión** (tras migrar redirecciones a PHP 8.5.12, riesgo 1): descarga, alta con búsqueda de coincidencias y borrado.
- [ ] **MongoDB 8,** si es la versión elegida: repetir 1.1 a 1.4.
- [ ] **Log de PHP de preproducción** sin `Deprecated` ni `Warning` del ACD tras una jornada de uso.

### 3. Resultados (28-10-2026)

Pruebas con el código de `366bb51` (el desempate de `5d4d06b` se revirtió en `ffd761b`, que deja el código igual). `57b342e` solo cambia la lectura de parámetros del editor y la carga de usuarios y enumerados; con ese código se repitió Playwright en MongoDB 9:

| Prueba | MongoDB 9.0 | MongoDB 7.0.45 |
|---|---|---|
| Playwright, 123 estructuras, 2 roles | 20/20, log de PHP vacío; con `57b342e`, 23/23 | 20/20, log de PHP vacío |
| Paridad de consultas (3.856, 2 roles) | Solo las 3 diferencias esperadas | Solo las 3 diferencias esperadas |
| Escritura (`smoke.php`) | Correcta | Igual que en MongoDB 9 |
| Contrato con redirecciones | 300/300 | 300/300 |

Con `57b342e` y MongoDB 9 también se enviaron como array, uno a uno, todos los parámetros de las 23 páginas y 13 formularios del editor (142 peticiones): ninguno acaba ya en TypeError. Y se comprobaron con los formularios reales el alta, la edición y la baja de usuarios, enumerados y estructuras, sin avisos en el log.

Además, con el código de `175ce41` y MongoDB 9 se hizo un recorrido HTTP más amplio: 3.182 páginas por rol, abriendo hasta 6 contenidos de cada estructura. No dio errores y el log de PHP quedó vacío.
