<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use Cake\Http\Server;
use Cake\Core\Configure;

$server = new Server(new Application(dirname(__DIR__) . '/config'));

try {
    $response = $server->run();

    echo "<h2>run() 後の設定値</h2><pre>";
    echo "App.encoding: " . var_export(Configure::read('App.encoding'), true) . "\n";
    echo "debug: " . var_export(Configure::read('debug'), true) . "\n";
    echo "Security.salt: " . (Configure::read('Security.salt') ? "set" : "NULL") . "\n";
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "</pre>";
} catch (\Throwable $e) {
    echo "<h2>例外発生</h2><pre>";
    echo "Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "App.encoding: " . var_export(Configure::read('App.encoding'), true) . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}