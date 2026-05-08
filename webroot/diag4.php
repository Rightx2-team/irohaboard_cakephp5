<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use Cake\Http\ServerRequestFactory;
use Cake\Http\Runner;
use Cake\Core\Configure;

$app = new Application(dirname(__DIR__) . '/config');
$app->bootstrap();

echo "<h2>bootstrap後</h2><pre>";
echo "App.encoding: " . var_export(Configure::read('App.encoding'), true) . "\n";
echo "</pre>";

$app->pluginBootstrap();
$middleware = $app->middleware($app->getMiddleware());

echo "<h2>middleware取得後</h2><pre>";
echo "App.encoding: " . var_export(Configure::read('App.encoding'), true) . "\n";
echo "</pre>";

$request = ServerRequestFactory::fromGlobals();

try {
    $runner = new Runner();
    $response = $runner->run($middleware, $request, $app);

    echo "<h2>正常終了</h2><pre>";
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "</pre>";
} catch (\Throwable $e) {
    echo "<h2>元エラー発生</h2><pre>";
    echo "Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Previous:\n";
    $p = $e->getPrevious();
    while ($p) {
        echo "  Class: " . get_class($p) . "\n";
        echo "  Message: " . $p->getMessage() . "\n";
        echo "  File: " . $p->getFile() . ":" . $p->getLine() . "\n\n";
        $p = $p->getPrevious();
    }
    echo "\nTrace:\n" . $e->getTraceAsString();
    echo "</pre>";
}