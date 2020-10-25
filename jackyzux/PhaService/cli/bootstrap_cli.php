<?php

use Phalcon\Di\FactoryDefault\Cli as FactoryDefault;
//use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Cli\Console\Extended as ConsoleApp;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/cli');

/**
 * The FactoryDefault Dependency Injector automatically registers the services that
 * provide a full stack framework. These default services can be overidden with custom ones.
 */
$di = new FactoryDefault();

/**
 * Include general services
 */
require BASE_PATH . '/cli/config/services.php';


/**
 * Include Autoloader
 */
require BASE_PATH . '/cli/config/loader.php';

/**
 * Get config service for use in inline setup below
 */
$config = $di->getConfig();

/**
 * Create a console application
 */
$console = new ConsoleApp($di);

/**
 * Process the console arguments
 */
//if($argc == 1){
//    $arguments['task'] = 'help';
//}else {
//    foreach ($argv as $k => $arg) {
//        if ($k == 1) {
//            $arguments['task'] = $arg;
//        } elseif ($k == 2) {
//            $arguments['action'] = $arg;
//        } elseif ($k >= 3) {
//            $arguments['params'][] = $arg;
//        }
//    }
//}


$arguments = [];
$params    = [];

foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}


try {

    /**
     * Handle
     */
    $console->handle($arguments);

    /**
     * If configs is set to true, then we print a new line at the end of each execution
     *
     * If we dont print a new line,
     * then the next command prompt will be placed directly on the left of the output
     * and it is less readable.
     *
     * You can disable this behaviour if the output of your application needs to don't have a new line at end
     */
    if (isset($config["printNewLine"]) && $config["printNewLine"]) {
        echo PHP_EOL;
    }

} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(255);
}
