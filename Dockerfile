FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

# Step 1: 换源 + PHP 8.4 PPA
RUN sed -i 's|http://archive.ubuntu.com|https://mirrors.aliyun.com|g' /etc/apt/sources.list.d/ubuntu.sources \
    && sed -i 's|http://security.ubuntu.com|https://mirrors.aliyun.com|g' /etc/apt/sources.list.d/ubuntu.sources \
    && apt-get update && apt-get install -y --no-install-recommends software-properties-common \
    && add-apt-repository -y ppa:ondrej/php \
    && rm -rf /var/lib/apt/lists/*

# Step 2: 安装系统包
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    php8.4-fpm \
    php8.4-mysql \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-bcmath \
    php8.4-curl \
    php8.4-zip \
    php8.4-gd \
    php8.4-intl \
    php8.4-opcache \
    mysql-server \
    supervisor \
    ca-certificates \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/lib/mysql/*

# Step 3: 安装 Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Step 4: 安装 Composer
RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Step 5: 换源（composer + npm）
RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
RUN npm config set registry https://registry.npmmirror.com

# Step 6: 复制应用代码
COPY src/ /var/www/myblog
WORKDIR /var/www/myblog

# Step 7: 安装PHP依赖
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && composer clear-cache

# Step 8: 构建前端
RUN npm install --ignore-scripts \
    && npm run build \
    && rm -rf node_modules

# Step 9: 复制Docker配置到系统路径
COPY docker/supervisord.conf /etc/supervisor/conf.d/myblog.conf
COPY docker/nginx.conf /etc/nginx/sites-available/myblog
COPY docker/mysql.cnf /etc/mysql/mysql.conf.d/myblog.cnf

RUN ln -sf /etc/nginx/sites-available/myblog /etc/nginx/sites-enabled/myblog \
    && rm -f /etc/nginx/sites-enabled/default

# Step 10: 配置PHP
RUN sed -i 's|^listen = .*|listen = 127.0.0.1:9000|' /etc/php/8.4/fpm/pool.d/www.conf \
    && sed -i 's|^;php_admin_value\[memory_limit\].*|php_admin_value[memory_limit] = 256M|' /etc/php/8.4/fpm/pool.d/www.conf \
    && sed -i 's|^upload_max_filesize = .*|upload_max_filesize = 20M|' /etc/php/8.4/fpm/php.ini \
    && sed -i 's|^post_max_size = .*|post_max_size = 20M|' /etc/php/8.4/fpm/php.ini \
    && sed -i 's|^;*opcache\.enable=.*|opcache.enable=1|' /etc/php/8.4/fpm/php.ini \
    && sed -i 's|^;*opcache\.memory_consumption=.*|opcache.memory_consumption=128|' /etc/php/8.4/fpm/php.ini \
    && sed -i 's|^;*opcache\.interned_strings_buffer=.*|opcache.interned_strings_buffer=8|' /etc/php/8.4/fpm/php.ini \
    && sed -i 's|^;*opcache\.max_accelerated_files=.*|opcache.max_accelerated_files=4000|' /etc/php/8.4/fpm/php.ini \
    && sed -i 's|^;*opcache\.validate_timestamps=.*|opcache.validate_timestamps=0|' /etc/php/8.4/fpm/php.ini

# Step 11: 目录权限
RUN mkdir -p storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Step 12: 入口脚本
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p /var/log/supervisor /var/log/mysql

EXPOSE 80
VOLUME /var/lib/mysql
VOLUME /var/www/myblog/storage/app/public

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/myblog.conf", "-n"]