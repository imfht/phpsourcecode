<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        APP_PATH . $config->application->modelsDir
    )
)->register();
