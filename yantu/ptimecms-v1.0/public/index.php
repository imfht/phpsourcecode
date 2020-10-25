<?php
error_reporting(E_ALL);

date_default_timezone_set("PRC");

$debug = new \Phalcon\Debug();
$debug->listen();


define('APP_PATH', realpath('..'));

// try {

    /**
     * Read the configuration
     */
    $config = include APP_PATH . "/app/config/config.php";

    /**
     * Read auto-loader
     */
    include APP_PATH . "/app/config/loader.php";

    /**
     * Read services
     */
    include APP_PATH . "/app/config/services.php";
    
    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

// } catch (\Exception $e) {
//      echo $e->getMessage();
// }

