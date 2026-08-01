#!/bin/sh
set -e

echo "=== Production Deployment ==="

# Build containers
docker compose build --no-cache

# Start services
docker compose up -d

# Wait for MySQL
echo "Waiting for MySQL..."
sleep 10

# Run migrations inside first PHP container
docker compose exec php-app-1 php artisan migrate --force

# Cache everything
docker compose exec php-app-1 php artisan config:cache
docker compose exec php-app-1 php artisan route:cache
docker compose exec php-app-1 php artisan view:cache
docker compose exec php-app-1 php artisan event:cache

# Clear and optimize
docker compose exec php-app-1 php artisan icon:cache 2>/dev/null || true
docker compose exec php-app-1 php artisan storage:link 2>/dev/null || true

# Restart Horizon
docker compose exec php-horizon-1 php artisan horizon:terminate 2>/dev/null || true

# Reload Nginx
docker compose exec nginx nginx -s reload

echo "=== Deployment Complete ==="
echo "App running at https://localhost"
