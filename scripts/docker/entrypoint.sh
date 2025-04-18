#!/bin/bash

set -Eeo pipefail
set -e

ROOT=/var/www/html
ARTISAN="php ${ROOT}/artisan"

STORAGE=${ROOT}/storage
mkdir -p ${STORAGE}/logs
mkdir -p ${STORAGE}/app/public
mkdir -p ${STORAGE}/framework/views
mkdir -p ${STORAGE}/framework/cache
mkdir -p ${STORAGE}/framework/sessions
chown -R www-data:www-data ${STORAGE}
chmod -R g+rw ${STORAGE}

if [ -z "${APP_KEY:-}" ]; then
    ${ARTISAN} key:generate --no-interaction
    key=$(grep APP_KEY .env | cut -c 9-)
    echo "APP_KEY generated: $key — save it for later usage."
else
    echo "APP_KEY already set."
fi

# Run migrations & setup
${ARTISAN} waitfordb
${ARTISAN} app:setup

php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
