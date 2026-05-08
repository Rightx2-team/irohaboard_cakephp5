<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use Cake\Http\ServerRequestFactory;
use Cake\Http\Runner;
use Cake\Http\MiddlewareQueue;
use Cake\Core\Configure;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

$app = new Application(dirname(__DIR__) . '/config');

// BaseApplicationのprotectedなbootstrap()を経由してInvokeするため、
// リフレクションで直接呼び出す
$ref = new ReflectionClass($app);
$bootstrapMethod = $ref->getMethod('bootstrap');
$bootstrapMethod->setAccessible(true);
$bootstrapMethod->invoke($app);

echo "<h2>bootstrap後</h2><pre>";
echo "App.encoding: " . var_export(Configure::read('App.encoding'), true) . "\n";
echo "debug: " . var_export(Configure::read('debug'), true) . "\n";
echo "</pre>";

// ErrorHandlerMiddlewareを入れずにミニマム実行
$queue = new MiddlewareQueue();
$queue->add(new AssetMiddleware([]));
$queue->add(new RoutingMiddleware($app));

$request = ServerRequestFactory::fromGlobals();
$runner = new Runner();

try {
    $response = $runner->run($queue, $request);
    echo "<h2>正常</h2><pre>Status: " . $response->getStatusCode() . "</pre>";
} catch (\Throwable $e) {
    echo "<h2>生エラー</h2><pre>";
    echo "Class: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}