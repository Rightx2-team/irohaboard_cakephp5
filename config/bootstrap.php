<?php
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Utility\Security;

if (!defined('WINDOWS')) {
    if (DIRECTORY_SEPARATOR === '\\' || substr(PHP_OS, 0, 3) === 'WIN') {
        define('WINDOWS', true);
    } else {
        define('WINDOWS', false);
    }
}

require CAKE . 'functions.php';

try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

if (!Configure::read('debug')) {
    Configure::write('Cache._cake_core_.duration', '+1 years');
    Configure::write('Cache._cake_model_.duration', '+1 years');
}

mb_internal_encoding(Configure::read('App.encoding') ?? 'UTF-8');

ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

date_default_timezone_set(Configure::read('App.defaultTimezone'));

(new ErrorTrap(Configure::read('Error')))->register();
(new ExceptionTrap(Configure::read('Error')))->register();

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Mailer::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

require __DIR__ . DIRECTORY_SEPARATOR . 'ib_config.php';