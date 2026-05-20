FROM harbor.xefi.fr/xefi/hopla/hopla-frankenstein-php:8.4
COPY .caddyfile/Caddyfile /etc/caddy/Caddyfile
WORKDIR /var/www/

COPY . /var/www/

RUN composer install --no-ansi --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader \
    && chown -R www-data:www-data .

COPY .supervisord/supervisord.conf /etc/supervisor/supervisord.conf

RUN cd /var/www/ && php artisan storage:link

CMD ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80", "--caddyfile=/etc/caddy/Caddyfile"]
