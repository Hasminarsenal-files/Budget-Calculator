#!/bin/sh
set -e

# Ensure writable storage and cache directories exist in /tmp
mkdir -p /tmp/storage/framework/views /tmp/storage/framework/sessions /tmp/storage/framework/cache /tmp/storage/logs /tmp/bootstrap/cache

export VIEW_COMPILED_PATH=/tmp/storage/framework/views
export APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php
export APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php
export APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php
export APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php
export APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php

# Run database migrations if DB host or URL is configured
if [ -n "$DB_HOST" ] || [ -n "$DB_URL" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || echo "Migration notice: could not complete migration."
fi

exec "$@"
