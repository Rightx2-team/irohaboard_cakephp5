<?php
// webroot/index.phpと同じ経路で起動
require dirname(__DIR__) . '/vendor/autoload.php';

$server = new \Cake\Http\Server(new \App\Application(dirname(__DIR__) . '/config'));
$server->bootstrap();

echo "<h2>bootstrap後の設定値</h2><pre>";
echo "App.encoding: " . var_export(\Cake\Core\Configure::read('App.encoding'), true) . "\n";
echo "debug: " . var_export(\Cake\Core\Configure::read('debug'), true) . "\n";
echo "Security.salt: " . (\Cake\Core\Configure::read('Security.salt') ? "set" : "NULL") . "\n";
echo "</pre>";