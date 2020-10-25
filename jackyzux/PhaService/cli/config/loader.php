<?php
$loader = new \Phalcon\Loader();

$loader->registerDirs([
    BASE_PATH . '/cli/tasks',
    BASE_PATH . '/cli/library',
    BASE_PATH . '/app/models',
    BASE_PATH . '/library',
]);

$loader->registerNamespaces([
    'PhaSvc' => BASE_PATH . '/library/PhaSvc',
]);

$loader->register();

require BASE_PATH . '/vendor/autoload.php'; //Composer Loader

if ($dev = file_exists(BASE_PATH . '/.development') || $test = file_exists(BASE_PATH . '/.testing')) {
    if (!defined('APP_DEBUGGER')) define('APP_DEBUGGER', 1);
    require __DIR__ . '/debugger.php';
    if (TRUE == $dev) define('APP_ENV', 'development');
    elseif (TRUE == $test) define('APP_ENV', 'testing');
}
if (!defined('APP_DEBUGGER')) define('APP_ENV', 'production');

