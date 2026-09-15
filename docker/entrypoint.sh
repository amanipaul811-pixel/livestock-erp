#!/usr/bin/env bash
set -euo pipefail

: "${PORT:=8080}"
export PORT

# Render assigns PORT at runtime; nginx config files can't read env vars
# directly, so render the template into the real config location first.
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-available/default
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
rm -f /etc/nginx/sites-enabled/default.orig 2>/dev/null || true

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "FATAL: APP_KEY is not set. Generate one with 'php artisan key:generate --show' and set it as an env var (do not regenerate on every deploy -- it decrypts existing sessions/data)." >&2
    exit 1
fi

# composer install ran with --no-scripts during the build (app code wasn't
# present yet at that layer, so package:discover couldn't run then) -- do it
# now that the full app + all PHP extensions are actually in place.
php artisan package:discover --ansi

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
