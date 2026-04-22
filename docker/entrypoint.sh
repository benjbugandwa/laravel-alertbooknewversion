#!/usr/bin/env bash
set -e

cd /app
php -r "echo getenv('DB_CONNECTION').PHP_EOL; echo getenv('DATABASE_URL').PHP_EOL;"

echo "==> Booting AlertBook…"

mkdir -p storage/app/livewire-tmp
mkdir -p storage/app/public/incidents
mkdir -p storage/app/public/case-notes
mkdir -p storage/app/public/referencements
chmod -R 775 storage bootstrap/cache

# 1) Toujours repartir d’une config propre (évite sqlite “collé”)
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 2) Debug (optionnel)
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo 'DB_CONNECTION='.config('database.default').PHP_EOL; echo 'DB_HOST='.config('database.connections.pgsql.host').PHP_EOL;"

# 3) Storage link
php artisan storage:link || true



# 4) MIGRATIONS D’ABORD
php artisan migrate --force

# 5) Ensuite seulement, cache (optionnel)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

#PORT="${PORT:-8080}"
#php artisan serve --host=0.0.0.0 --port="$PORT"
# Nettoie le port si Railway envoie quelque chose comme "0.0.0.0:8080" ou "tcp://..."

PORT="${PORT:-8080}"
PORT="$(echo "$PORT" | tr -cd '0-9')"
if [ -z "$PORT" ]; then PORT=8080; fi

php -S 0.0.0.0:${PORT} -t public