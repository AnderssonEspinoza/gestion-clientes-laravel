#!/usr/bin/env bash
set -euo pipefail

mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions bootstrap/cache
chmod -R 775 storage bootstrap/cache
php artisan config:clear
php artisan view:clear
