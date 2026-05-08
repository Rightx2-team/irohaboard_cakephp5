#!/bin/sh
set -e

# Generate app_local.php from environment variables
# 環境変数から app_local.php を生成
cat > /var/www/html/config/app_local.php << 'PHPEOF'
<?php
return [
    'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN),
    'Security' => [
        'salt' => getenv('SECURITY_SALT') ?: 'default_salt_please_change_in_production',
    ],
    'Session' => [
        'defaults' => 'php',
        'cookie'   => 'CAKEPHP',
        'timeout'  => 120,
        'ini'      => ['session.cookie_httponly' => true],
    ],
    'Datasources' => [
        'default' => [
            'host'     => getenv('DB_HOST')     ?: 'db',
            'username' => getenv('DB_USERNAME') ?: 'irohaboard',
            'password' => getenv('DB_PASSWORD') ?: 'password',
            'database' => getenv('DB_DATABASE') ?: 'irohaboard',
            'driver'   => 'Cake\Database\Driver\Mysql',
            'encoding' => 'utf8mb4',
            'url'      => getenv('DATABASE_URL') ?: null,
        ],
        'test' => [
            'host'     => getenv('DB_HOST')     ?: 'db',
            'username' => getenv('DB_USERNAME') ?: 'irohaboard',
            'password' => getenv('DB_PASSWORD') ?: 'password',
            'database' => getenv('DB_DATABASE') ?: 'irohaboard',
            'driver'   => 'Cake\Database\Driver\Mysql',
            'encoding' => 'utf8mb4',
        ],
    ],
];
PHPEOF

# Fix RewriteBase in webroot/.htaccess for container root serving
# コンテナのルートで動作するよう webroot/.htaccess の RewriteBase を修正
sed -i 's|RewriteBase .*|RewriteBase /|' /var/www/html/webroot/.htaccess

# Set file permissions
# ファイルパーミッションを設定
chown -R www-data:www-data \
    /var/www/html/tmp \
    /var/www/html/logs \
    /var/www/html/files \
    /var/www/html/webroot/files \
    /var/www/html/webroot/uploads \
    2>/dev/null || true

exec "$@"
