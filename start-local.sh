#!/bin/sh

composer install --no-ansi --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader &&
chown -R www-data:www-data .

cd /var/www/ && php artisan storage:link
composer require laravel/octane && php artisan octane:install --server=frankenphp

php artisan migrate
php artisan key:generate
php artisan jwt:secret
php artisan horizon &

cp .caddyfile/Caddyfile /etc/caddy/Caddyfile
cp .caddyfile/php-custom.ini /usr/local/etc/php/conf.d/php-custom.ini

php artisan octane:frankenphp --host=0.0.0.0 --port=80 --caddyfile=/etc/caddy/Caddyfile
