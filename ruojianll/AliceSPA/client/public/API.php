<?php
error_reporting(E_ALL);
use Phalcon\Mvc\Micro;

define('APP_PATH', realpath('../../server'));

try {

    /**
     * Read the configuration
     */
    $config = new \Phalcon\Config\Adapter\Ini(__DIR__ . "/../../server/config/config.ini");

    /**
     * Include Services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Starting the application
     * Assign service locator to the application
     */
    $app = new Micro($di);

    /**
     * Incude Application
     */
 //   include APP_PATH . '/app.php';

    include APP_PATH . '/API/index.php';
    /**
     * Handle the request
     */
    $app->handle();

} catch (\Exception $e) {
    echo $e->getMessage();
}
