<script language="javascript">
const valueSession = "Probando la sesion "+ Math.floor(Math.random() * 100);
fetch('./test_health_session.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'}, // this line is important, if this content-type is not set it wont work
    body: 'valorSession=' + valueSession
})
.then(response => response.text())
.then(data => {
    document.getElementById("establecido").innerHTML = data;
//    alert(JSON.stringify(data))
    //Pedimos por get el valor de la sesion.
    fetch('./test_health_session.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById("esperado").innerHTML = "\""+data+"\"";
        })
        .finally(() => {
            const elemEstablecido = document.getElementById("establecido");
            elemEstablecido.innerHTML = "\""+elemEstablecido.innerHTML+"\"";
            const elemEsperado = document.getElementById("esperado");
            //console.log("Finally",elemEsperado, elemEsperado.innerHTML, elemEstablecido, elemEstablecido.innerHTML )
            elemEstablecido.innerHTML === elemEsperado.innerHTML ? elemEsperado.append('🟢 ok'):elemEsperado.append('🔴 fail')

        });
})
.catch (error => {
    console.log('Request failed', error);
})

</script>
<?php
//Establecer para cada entorno la configuración.
if (file_exists('../config/conf.php')) {
    require '../config/conf.php';
}
///require_once __DIR__.'/../../../conf/neo/EnvLoad.php';
ini_set('session.gc_maxlifetime', $_ENV['ACD_SESSION_GC_MAXLIFETIME']);
session_start();

function dump($name, $variable) {
    echo "\n<br/>$name: ".var_export($variable, true);
}
function test($name, $result) {
    echo "\n<br/>$name: ".
    resultToString($result);
}

function resultToString($result) {
    return $result ? "🟢 ok"
    : "🔴 fail";
}
function expectEqual($value, $expectedValue) {
    return $value === $expectedValue;
}
function expectNull($value) {
    return $value === null;
}
function expectString($value) {
    return gettype($value) === "string";
}
function expectObjectMongo($value){
    return gettype($value) === "object" && is_a($value, 'MongoDB\Driver\Manager') ;
}
echo "<h1>Session</h1>";
dump("session_save_handler", ini_get('session.save_handler'));
dump("session_save_path", session_save_path());
dump("session_id", session_id());
test("Current session status",
    session_status() === PHP_SESSION_ACTIVE
);//session_status() === PHP_SESSION_NONE no existe session creada.
//$valueSession = 'Probando que funciona la sesion"';
echo "<div> Session setted <span id=\"establecido\"></span></div>";
echo "<div>Session getted <span id=\"esperado\"></span></div>";

echo "<h1>Environment variables</h1>";
test (
    "Environment variable ACD_DIR_TEMPLATES is setted",
    expectString($_ENV["ACD_DIR_TEMPLATES"])
);
test (
    "Environment variable ACD_not_setted is not setted",
    !isset($_ENV["ACD_not_seted"])
);

echo "<h1>Values</h1>";
foreach (get_defined_vars()["_ENV"] as $key => $value) {
    dump($key, $value);
}
echo "<h1>Mongo BBDD</h1>";

$serverMongo = getenv('ACD_MONGODB_SERVER');
$bbdd = getenv('ACD_MONGODB_DB');
//$bbdd = "ACFDERE";
dump("SERVER", $serverMongo);
dump("DB", $bbdd);
$collection = "user";
//Conectamos con el servidor.
//NEO_MONGODB_SERVER
try {

    $mng = new MongoDB\Driver\Manager($serverMongo);
    test (
        "Connect to bbdd server is",
        expectObjectMongo($mng)
    );

} catch (MongoDB\Driver\Exception\Exception $e) {

    test (
          "No connect to bbdd server, error:".$e->getMessage(),
          false
    );

}
if (isset($mng)) {
    try {
        $filtros = []; //obtengo todos los datos
        $query = new MongoDB\Driver\Query($filtros);
        $cursor = $mng->executeQuery("$bbdd.$collection", $query)->toArray();
        test(
            "Existen registros en $collection (".count($cursor).")",
            count($cursor) > 0
        );
        /*echo "<h2>Usuarios</h2>";
        dump("Usuarios, hay ",count($cursor));
        foreach($cursor as $document) {
            //echo "<br>";print_r($document);
            dump("id", $document->_id);
        }*/
    } catch (MongoDB\Driver\Exception\Exception $e) {
        test (
            "Error al obtener los datos de los usuarios: ".$e->getMessage().var_export($cursor,true),
            false
        );
    } catch (\Exception $e){ //NOTA eliminarlo???
        test (
            "Error al ejecutar la query: ". $e->getMessage(),
            false
        );
    }
}
