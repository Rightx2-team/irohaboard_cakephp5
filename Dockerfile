FROM php:8.4-apache

# Install system packages / システムパッケージのインストール
RUN apt-get update \
    && apt-get install -y libicu-dev libonig-dev libzip-dev unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions / PHP拡張をインストール
RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip

# Enable Apache mod_rewrite / Apache mod_rewrite を有効化
RUN a2enmod rewrite

# Copy PHP and Apache configuration / PHP・Apache設定をコピー
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy application source / アプリケーションをコピー
COPY . /var/www/html/

# Set file permissions / ファイルパーミッションを設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/tmp \
    && chmod -R 777 /var/www/html/logs \
    && chmod -R 777 /var/www/html/files

# Set entrypoint script / エントリポイントスクリプトを設定
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
