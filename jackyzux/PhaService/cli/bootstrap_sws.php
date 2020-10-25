<?php

use Phalcon\Di\FactoryDefault\Cli as FactoryDefault;
use Phalcon\Cli\Console as Application;

if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
if (!defined('APP_PATH')) define('APP_PATH', BASE_PATH . '/cli');

require BASE_PATH . '/vendor/autoload.php'; //Composer Loader

/**
 * Env settings
 */
if ($dev = file_exists(BASE_PATH . '/.development') || $test = file_exists(BASE_PATH . '/.testing')) {
    if (!defined('APP_DEBUGGER')) define('APP_DEBUGGER', 1);
    require BASE_PATH . '/cli/config/debugger.php';
    if (TRUE == $dev) define('APP_ENV', 'development');
    elseif (TRUE == $test) define('APP_ENV', 'testing');
}
if (!defined('APP_DEBUGGER')) define('APP_ENV', 'production');


$di = new FactoryDefault();

require BASE_PATH . '/cli/config/services.php';


/**
 * Loader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs([
    BASE_PATH . '/cli/wsockets',
    BASE_PATH . '/cli/library',
    BASE_PATH . '/app/models',
    BASE_PATH . '/library',
]);

$loader->registerNamespaces([
    'PhaSvc' => BASE_PATH . '/library/PhaSvc',
]);

$loader->register();


/**
 * return Application
 */
$application = new Application($di);

$application->config->tasksDir                    = BASE_PATH . '/cli/wsockets';
$application->config->application->controllersDir = BASE_PATH . '/cli/wsockets/';

return $application;

