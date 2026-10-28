#!/usr/bin/env bash
# Entorno local para las pruebas E2E del editor ACD:
# MongoDB + Apache con PHP 8.5.12 sirviendo este repositorio.
#
# Uso:
#   tests/e2e/entorno.sh up      Levanta MongoDB y el editor en http://127.0.0.1:$ACD_E2E_PORT/
#   tests/e2e/entorno.sh test    Ejecuta Playwright (imagen Docker) contra el editor
#   tests/e2e/entorno.sh log     Muestra el log de errores de PHP del editor
#   tests/e2e/entorno.sh down    Para los contenedores y borra la red
#
# Variables (en el entorno o en tests/e2e/.env):
#   ACD_E2E_DUMP          mongodump --archive de la base acd que se restaura (opcional)
#   ACD_E2E_MONGO_IMAGE   imagen de MongoDB (por defecto mongo:9.0)
#   ACD_E2E_PHP_IMAGE     imagen de PHP con Apache
#   ACD_E2E_PW_IMAGE      imagen con Playwright y navegadores
#   ACD_E2E_PORT          puerto local del editor (por defecto 18085)
#   ACD_E2E_DEVELOPER_USER, ACD_E2E_EDITOR_USER, ACD_E2E_PASSWORD  usuarios de prueba que se crean
set -euo pipefail

E2E_DIR="$(cd "$(dirname "$0")" && pwd)"
ROOT="$(cd "$E2E_DIR/../.." && pwd)"
if [ -f "$E2E_DIR/.env" ]; then
	# Las variables ya definidas en el entorno tienen prioridad
	while IFS='=' read -r name value; do
		[[ -z "$name" || "$name" == \#* ]] && continue
		value="${value%\"}"; value="${value#\"}"
		[ -z "${!name:-}" ] && export "$name=$value"
	done < <(grep -E '^[A-Z0-9_]+=' "$E2E_DIR/.env")
fi

NETWORK=acd-e2e
MONGO=acd-e2e-mongo
WEB=acd-e2e-web
MONGO_IMAGE="${ACD_E2E_MONGO_IMAGE:-mongo:9.0}"
PHP_IMAGE="${ACD_E2E_PHP_IMAGE:-registry-cicd.apps.ocpmovistar001.interactivos.int/bob/fau/config/movistarbaseimage:php8.5.12}"
PW_IMAGE="${ACD_E2E_PW_IMAGE:-movistar-playwright-testing:latest}"
PORT="${ACD_E2E_PORT:-18085}"
WORK="$E2E_DIR/.entorno"

up() {
	grep -q '"name": "mongodb/mongodb",\s*$' "$ROOT/vendor/composer/installed.json" 2>/dev/null &&
		grep -A1 '"name": "mongodb/mongodb"' "$ROOT/vendor/composer/installed.json" | grep -q '"version": "2\.' ||
		{ echo "vendor/ no corresponde a esta rama: ejecuta composer install --ignore-platform-reqs en $ROOT" >&2; exit 1; }
	[ -n "${ACD_E2E_PASSWORD:-}" ] || { echo "Falta ACD_E2E_PASSWORD" >&2; exit 1; }

	mkdir -p "$WORK/content/acd/contents" "$WORK/content/acd/auth_permanent_login" "$WORK/log"
	cp "$ROOT/test/data/permission.json" "$WORK/content/acd/"
	: > "$WORK/log/php-errors.log"
	cat > "$WORK/php.ini" <<-EOF
		error_reporting = E_ALL
		display_errors = Off
		log_errors = On
		error_log = /e2e-log/php-errors.log
		xdebug.mode = off
	EOF
	# Configuración del contenedor: .env.example sin comillas, como variables de entorno
	grep -E '^[A-Z0-9_]+=' "$ROOT/.env.example" | sed -E 's/="(.*)"$/=\1/' > "$WORK/acd.env"
	chmod -R a+rwX "$WORK"

	docker network create "$NETWORK" >/dev/null
	docker run -d --rm --name "$MONGO" --network "$NETWORK" "$MONGO_IMAGE" >/dev/null
	for _ in $(seq 60); do
		docker exec "$MONGO" mongosh --quiet --eval 'db.runCommand({ping:1}).ok' 2>/dev/null | grep -q 1 && break
		sleep 1
	done
	if [ -n "${ACD_E2E_DUMP:-}" ]; then
		docker exec -i "$MONGO" mongorestore --quiet --archive < "$ACD_E2E_DUMP"
	fi

	local hash
	hash=$(docker run --rm --entrypoint php "$PHP_IMAGE" -r 'echo password_hash($argv[1], PASSWORD_BCRYPT);' "$ACD_E2E_PASSWORD")
	docker exec "$MONGO" mongosh --quiet acd --eval "
		db.user.updateOne({_id: '${ACD_E2E_DEVELOPER_USER:-e2e_developer}'}, {\$set: {password: '$hash', rol: 'developer'}}, {upsert: true});
		db.user.updateOne({_id: '${ACD_E2E_EDITOR_USER:-e2e_editor}'}, {\$set: {password: '$hash', rol: 'editor'}}, {upsert: true});" >/dev/null

	# Las variables del contenedor tienen prioridad sobre cualquier .env del repositorio
	docker run -d --rm --name "$WEB" --network "$NETWORK" -p "127.0.0.1:$PORT:80" \
		--env-file "$WORK/acd.env" -e ACD_MONGODB_SERVER="mongodb://$MONGO:27017" -e ACD_MONGODB_DB=acd \
		-e ACD_PERMISSION_PATH=/mnt/content/acd/permission.json \
		-e ACD_DATA_CONTENT_PATH=/mnt/content/acd/contents \
		-v "$ROOT:/var/www/acd:ro" \
		-v "$WORK/content:/mnt/content" -v "$WORK/log:/e2e-log" \
		-v "$WORK/php.ini:/usr/local/etc/php/conf.d/zz-e2e.ini:ro" \
		--entrypoint sh "$PHP_IMAGE" \
		-c 'sed -ri "s!/var/www/html!/var/www/acd/public!g" /etc/apache2/sites-available/*.conf && exec docker-php-entrypoint apache2-foreground' >/dev/null

	for _ in $(seq 30); do
		curl -sf -o /dev/null "http://127.0.0.1:$PORT/index.php" && break
		sleep 1
	done
	echo "Editor en http://127.0.0.1:$PORT/ (log de PHP: $WORK/log/php-errors.log)"
}

run_tests() {
	# La imagen trae node_modules en /app; la suite se monta debajo para resolverlos
	docker run --rm --network "$NETWORK" --ipc=host \
		-e ACD_E2E_BASE_URL="http://$WEB/" \
		-e ACD_E2E_DEVELOPER_USER -e ACD_E2E_EDITOR_USER -e ACD_E2E_PASSWORD \
		-e ACD_E2E_STRUCTURE -e ACD_E2E_RELATED_STRUCTURE -e ACD_E2E_RELATED_SEARCH -e ACD_E2E_MAX_STRUCTURES \
		-v "$E2E_DIR:/app/acd-e2e" -w /app/acd-e2e \
		--entrypoint npx "$PW_IMAGE" playwright test "$@"
}

down() {
	docker stop "$WEB" "$MONGO" >/dev/null 2>&1 || true
	docker network rm "$NETWORK" >/dev/null 2>&1 || true
}

case "${1:-}" in
	up) up ;;
	test) shift; run_tests "$@" ;;
	log) cat "$WORK/log/php-errors.log" ;;
	down) down ;;
	*) sed -n '2,17p' "$0"; exit 1 ;;
esac
