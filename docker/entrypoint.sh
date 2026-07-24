#!/bin/sh
set -e

# Configurar a porta do Nginx com base na variável $PORT fornecida pelo Railway
PORT_TO_USE="${PORT:-80}"
sed -i "s/listen 80;/listen ${PORT_TO_USE};/g" /etc/nginx/http.d/default.conf 2>/dev/null || true
sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT_TO_USE};/g" /etc/nginx/http.d/default.conf 2>/dev/null || true

# Garantir permissões de escrita em storage e cache
mkdir -p /var/www/storage/logs /var/www/storage/framework/views /var/www/storage/framework/cache /var/www/bootstrap/cache /run/nginx
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/log/nginx /var/lib/nginx /run/nginx 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Executar automigrações no Railway se a BD estiver ativa
php /var/www/artisan migrate --force || true

# Limpar caches de desenvolvimento
php /var/www/artisan config:clear || true
php /var/www/artisan route:clear || true
php /var/www/artisan view:clear || true

# Iniciar o Supervisord (PHP-FPM + Nginx + Horizon + Scheduler)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
