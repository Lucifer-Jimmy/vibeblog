#!/bin/bash
set -e

APP_DIR="/var/www/myblog"
MYSQL_DATA_DIR="/var/lib/mysql"

DB_DATABASE="${DB_DATABASE:-myblog}"
DB_USERNAME="${DB_USERNAME:-myblog}"
DB_PASSWORD="${DB_PASSWORD:-myblog_secret}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-root_secret}"
ADMIN_NAME="${ADMIN_NAME:-Admin}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@myblog.test}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin123456}"
APP_URL="${APP_URL:-http://localhost}"
APP_NAME="${APP_NAME:-MyBlog}"

# Phase A: MySQL首次初始化
if [ ! -f "${MYSQL_DATA_DIR}/.initialized" ]; then
    echo ">>> 初始化MySQL..."
    mysqld --initialize-insecure --user=mysql --datadir="${MYSQL_DATA_DIR}"

    mysqld_safe --user=mysql &
    MYSQL_TEMP_PID=$!

    for i in $(seq 1 60); do
        if mysqladmin ping -h 127.0.0.1 --silent 2>/dev/null; then break; fi
        if [ "$i" -eq 60 ]; then echo "!!! MySQL超时"; exit 1; fi
        sleep 2
    done

    mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS ${DB_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER 'root'@'localhost' IDENTIFIED BY '${DB_ROOT_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%';
GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'localhost';
FLUSH PRIVILEGES;
SQL

    mysqladmin -u root -p"${DB_ROOT_PASSWORD}" shutdown
    wait $MYSQL_TEMP_PID 2>/dev/null || true
    sleep 2
    touch "${MYSQL_DATA_DIR}/.initialized"
fi

# Phase B: .env生成
if [ ! -f "${APP_DIR}/.env" ]; then
    cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"

    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" "${APP_DIR}/.env"
    sed -i "s|^# DB_HOST=.*|DB_HOST=127.0.0.1|" "${APP_DIR}/.env"
    sed -i "s|^# DB_PORT=.*|DB_PORT=3306|" "${APP_DIR}/.env"
    sed -i "s|^# DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" "${APP_DIR}/.env"
    sed -i "s|^# DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" "${APP_DIR}/.env"
    sed -i "s|^# DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" "${APP_DIR}/.env"

    sed -i "s|^APP_ENV=.*|APP_ENV=production|" "${APP_DIR}/.env"
    sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" "${APP_DIR}/.env"
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" "${APP_DIR}/.env"
    sed -i "s|^APP_NAME=.*|APP_NAME=${APP_NAME}|" "${APP_DIR}/.env"
    sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|" "${APP_DIR}/.env"

    echo "" >> "${APP_DIR}/.env"
    echo "ADMIN_NAME=${ADMIN_NAME}" >> "${APP_DIR}/.env"
    echo "ADMIN_EMAIL=${ADMIN_EMAIL}" >> "${APP_DIR}/.env"
    echo "ADMIN_PASSWORD=${ADMIN_PASSWORD}" >> "${APP_DIR}/.env"
    echo "DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}" >> "${APP_DIR}/.env"
fi

# Phase C: APP_KEY
if grep -q "^APP_KEY=$" "${APP_DIR}/.env"; then
    php artisan key:generate --force
fi

# Phase D: 权限与软链接
chown -R www-data:www-data "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
php artisan storage:link --force 2>/dev/null || true

# Phase E: 手动启动MySQL完成迁移
echo ">>> 启动MySQL..."
mysqld_safe --user=mysql &
MYSQL_PID=$!

for i in $(seq 1 60); do
    if mysqladmin ping -h 127.0.0.1 -u root -p"${DB_ROOT_PASSWORD}" --silent 2>/dev/null; then break; fi
    if [ "$i" -eq 60 ]; then echo "!!! MySQL启动超时"; exit 1; fi
    sleep 2
done

echo ">>> 数据库迁移..."
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">>> 停止临时MySQL..."
mysqladmin -u root -p"${DB_ROOT_PASSWORD}" shutdown
wait $MYSQL_PID 2>/dev/null || true
sleep 2

# Phase F: 前台启动supervisord管理所有服务
echo ">>> 启动所有服务..."
exec supervisord -c /etc/supervisor/conf.d/myblog.conf -n