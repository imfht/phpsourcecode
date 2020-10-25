<?php

const EXT = '.php';

define('APP_DIR', ROOT_DIR . 'app' . DIRECTORY_SEPARATOR);
define('LEN_DIR', ROOT_DIR . 'Len' . DIRECTORY_SEPARATOR);
define('LOGS_DIR', ROOT_DIR . 'logs' . DIRECTORY_SEPARATOR);
define('VENDOR_DIR', ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR);
define('PUBLIC_DIR', ROOT_DIR . 'public' . DIRECTORY_SEPARATOR);
define('CONFIG_DIR', ROOT_DIR . 'config' . DIRECTORY_SEPARATOR);
define('COMMON_DIR', LEN_DIR . 'Common' . DIRECTORY_SEPARATOR);
define('MODEL_DIR', APP_DIR . 'models' . DIRECTORY_SEPARATOR);
define('CONTROLLER_DIR', APP_DIR . 'controllers' . DIRECTORY_SEPARATOR);

define('IS_CLI', (defined('PHP_SAPI') && PHP_SAPI === 'cli') || php_sapi_name() === 'cli');
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

// Composer Library
require VENDOR_DIR . 'autoload.php';

// Script start time
define('BEGIN_TIME', milliSecond());

if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_error', 'On');
    !IS_CLI && \Core::_whoopsRegister();
} else {
    error_reporting(0);
    ini_set('display_error', 'Off');
    set_exception_handler(array('\Core', 'ErrorHandler'));
}

\Core::run(CONFIG_DIR . 'application.php');