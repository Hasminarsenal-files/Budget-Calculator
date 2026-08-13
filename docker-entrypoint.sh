#!/bin/sh
set -e

PORT="${PORT:-8000}"

# Ensure storage and cache directories exist in /tmp & storage
mkdir -p /tmp/storage/framework/views /tmp/storage/framework/sessions /tmp/storage/framework/cache /tmp/storage/logs /tmp/bootstrap/cache storage/framework/views storage/framework/sessions storage/framework/cache storage/logs bootstrap/cache

chmod -R 777 storage bootstrap/cache /tmp/storage /tmp/bootstrap 2>/dev/null || true

export VIEW_COMPILED_PATH=/tmp/storage/framework/views
export APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php
export APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php
export APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php
export APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php
export APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php
export LOG_CHANNEL=stderr
export APP_MAINTENANCE_DRIVER=file
export APP_MAINTENANCE_STORE=file

# Run database migrations if DB_HOST or DB_URL is configured
if [ -n "$DB_HOST" ] || [ -n "$DB_URL" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration skipped or database not ready."
fi

# Run artisan serve bound to 0.0.0.0:$PORT
if [ "$1" = "php" ] && [ "$2" = "artisan" ] && [ "$3" = "serve" ]; then
    echo "Starting Laravel server on 0.0.0.0:${PORT}..."
    exec php artisan serve --host=0.0.0.0 --port="${PORT}"
fi

exec "$@"
