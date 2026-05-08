<?php
require dirname(__DIR__) . '/config/paths.php';
require dirname(__DIR__) . '/vendor/autoload.php';

if (!function_exists('env')) {
    require dirname(__DIR__) . '/vendor/cakephp/cakephp/src/functions.php';
}

$config = require dirname(__DIR__) . '/config/app.php';
\Cake\Core\Configure::write($config);

$localConfig = require dirname(__DIR__) . '/config/app_local.php';
\Cake\Core\Configure::write($localConfig);

echo "<h2>設定値確認</h2><pre>";
echo "App.encoding: " . var_export(\Cake\Core\Configure::read('App.encoding'), true) . "\n";
echo "debug: " . var_export(\Cake\Core\Configure::read('debug'), true) . "\n";
echo "Security.salt: " . (\Cake\Core\Configure::read('Security.salt') ? "set" : "NULL") . "\n";
echo "</pre>";