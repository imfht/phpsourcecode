<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'PhaSvc' => BASE_PATH . '/library/PhaSvc',
]);


$loader->registerDirs(
    [
        //$config->application->controllersDir,
        //$config->application->modelsDir
        APP_PATH . '/controllers',
        APP_PATH . '/models',
        BASE_PATH . '/library',
    ]
);


$loader->register();

require BASE_PATH . '/vendor/autoload.php'; //Composer Loader

if ($dev = file_exists(BASE_PATH . '/.development') || $test = file_exists(BASE_PATH . '/.testing')) {
    define('APP_DEBUGGER', 1);
    require __DIR__ . '/debugger.php';
    if (TRUE == $dev) define('APP_ENV', 'development');
    elseif (TRUE == $test) define('APP_ENV', 'testing');
}
if (!defined('APP_DEBUGGER')) define('APP_ENV', 'production');

