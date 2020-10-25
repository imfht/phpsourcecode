<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */

use Phalcon\Config\Adapter\Ini as ConfigIni;
$config = new ConfigIni(APP_PATH . "/config/config.ini");
include_once "/config/loader.php";
include_once "/config/services.php";
$app = new Micro();
include_once "/API/index.php";
$app->handle();
/**
 * Not found handler
 */
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
