<?php

define('PA_PHP_ANALYSIS', true);
define('PA_ROOT_PATH', dirname(__DIR__));

$loader = require __DIR__ . '/../nimble/src/Nimble/nimble.php';
$loader->setPrefix('App', [ PA_ROOT_PATH . '/app' ]);

$bootConfig = [
    'app_path' => PA_ROOT_PATH . '/app',
    'config_path' => PA_ROOT_PATH . '/config',
];

$app = Nimble\Foundation\Bootstrap::application(
    $bootConfig,
    Nimble\Http\Application::class
);

function config($key)
{
    global $app;
    return $app->configure($key);
}

function app()
{
    global $app;
    return $app;
}

return $app;
