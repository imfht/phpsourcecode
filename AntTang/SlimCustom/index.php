<?php

/**
 * SlimCustom
 *
 * @author Jing Tang <tangjing3321@gmail.com>
 */

/*
 |--------------------------------------------------------------------------
 | Register The Auto Loader
 |--------------------------------------------------------------------------
 |
 | Composer provides a convenient, automatically generated class loader for
 | our application. We just need to utilize it! We'll simply require it
 | into the script here so that we don't have to worry about manual
 | loading any of our classes later on. It feels nice to relax.
 |
 */
require __DIR__ . '/bootstrap/autoload.php';

/*
 |--------------------------------------------------------------------------
 | Require SlimCustom Application
 |--------------------------------------------------------------------------
 | From SlimCustom application are introduced, and then start your tour.
 | Just simple !
 |
 */
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

$application = new \SlimCustom\Libs\App([
    'name' => \SlimCustom\Libs\App::NAME,
    'path' => realpath(__DIR__)
]);

return $application;