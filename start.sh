#!/bin/bash

# ============================================================================
# Railway PostgreSQL - Use Railway DATABASE_URL from environment
# ============================================================================

# Force correct database connection (fallback if env vars not set)
export DB_CONNECTION=pgsql
export DB_HOST=acela.proxy.rlwy.net
export DB_PORT=39458
export DB_DATABASE=railway
export DB_USERNAME=postgres
export DB_PASSWORD=JGXcLkkXfFlQncnEYiAyKMGmqPHwKKGu
export DB_SSLMODE=require

# Use Railway DATABASE_URL if available
if [ -n "$DATABASE_URL" ]; then
    echo "Using DATABASE_URL: ${DATABASE_URL%@*}"
else
    echo "Using fallback DB credentials"
fi

# Clear any cached config
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Run migrations (ignore errors if already run)
php artisan migrate --force || echo "Migration may have already run"

# Create storage symlink
php artisan storage:link 2>/dev/null || true

# Start the server
exec php artisan serve --host=0.0.0.0 --port=8000
