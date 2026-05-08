<?php
require __DIR__ . '/config/paths.php';
require __DIR__ . '/vendor/autoload.php';

if (!function_exists('env')) {
    require __DIR__ . '/vendor/cakephp/cakephp/src/functions.php';
}

use Cake\Core\Configure;

$config = require __DIR__ . '/config/app.php';
Configure::write($config);

$localConfig = require __DIR__ . '/config/app_local.php';
Configure::write($localConfig);

echo "App.encoding: " . var_export(Configure::read('App.encoding'), true) . PHP_EOL;
echo "debug: " . var_export(Configure::read('debug'), true) . PHP_EOL;
echo "Security.salt: " . (Configure::read('Security.salt') ? "set" : "NULL") . PHP_EOL;
echo "App.namespace: " . var_export(Configure::read('App.namespace'), true) . PHP_EOL;