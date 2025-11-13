echo '#!/bin/bash
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear' > heroku-postdeploy.sh