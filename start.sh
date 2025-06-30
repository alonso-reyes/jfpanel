#!/bin/bash

echo "=== Iniciando aplicación Laravel ==="

# Verificar si hay migraciones pendientes
PENDING_MIGRATIONS=$(php artisan migrate:status --pending 2>/dev/null | grep -c "Pending" || echo "0")

if [ "$PENDING_MIGRATIONS" -gt 0 ]; then
    echo "Se encontraron $PENDING_MIGRATIONS migraciones pendientes. Ejecutando..."
    php artisan migrate --force
    echo "Migraciones completadas."
else
    echo "No hay migraciones pendientes."
fi

# Optimizar para producción
echo "Optimizando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Iniciando Apache ==="
exec apache2-foreground