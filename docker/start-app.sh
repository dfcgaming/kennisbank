#!/bin/sh
set -eu

if [ -n "${APP_CREDENTIALS_FILE:-}" ]; then
    while IFS= read -r line; do
        case "$line" in
            APP_KEY=*) export APP_KEY="${line#APP_KEY=}" ;;
            DB_PASSWORD=*) export DB_PASSWORD="${line#DB_PASSWORD=}" ;;
            REDIS_PASSWORD=*) export REDIS_PASSWORD="${line#REDIS_PASSWORD=}" ;;
        esac
    done < "$APP_CREDENTIALS_FILE"
    : "${APP_KEY:?Missing APP_KEY in the runtime environment file}"
    : "${DB_PASSWORD:?Missing DB_PASSWORD in the runtime environment file}"
    : "${REDIS_PASSWORD:?Missing REDIS_PASSWORD in the runtime environment file}"
fi

php artisan migrate --force
exec frankenphp php-server --root public --listen :80
