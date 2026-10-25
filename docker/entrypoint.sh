#!/bin/sh
set -e
cd /var/www/html

APP_ENV_VAL="$(grep -E '^APP_ENV=' .env 2>/dev/null | head -n1 | cut -d= -f2- | tr -d '[:space:]' || true)"
APP_ENV_VAL="${APP_ENV_VAL:-production}"

if [ ! -f vendor/autoload.php ]; then
  if [ "$APP_ENV_VAL" = "production" ]; then
    composer install --no-dev --no-interaction --no-progress --optimize-autoloader
  else
    composer install --no-interaction --no-progress --optimize-autoloader
  fi
fi

if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --force
  sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
  sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env
  sed -i "s#^APP_URL=.*#APP_URL=https://aibotia.com#" .env
fi

if [ ! -d public/build ] && [ ! -f public/hot ] && [ -f package.json ]; then
  npm ci --no-audit --no-fund
  npm run build
fi

mkdir -p storage/framework/sessions storage/framework/cache storage/framework/views storage/logs bootstrap/cache
php artisan storage:link --force 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

php artisan migrate --force || true

exec "$@"
