<?php
define('PROJECT_PATH', realpath(__DIR__ . '/..') . '/');
define('APP_ENV', 'product');
define('APP_PATH', __DIR__ . '/App/');
define('TEST_SRC', __DIR__ . '/Cases/');
define('TEST_APP', __DIR__ . '/App/');
define('SY_PATH', PROJECT_PATH . 'src/');
define('SY_UNIT', 1);
define('SY_TEST', __DIR__ . '/');
require(PROJECT_PATH . '/vendor/autoload.php');

Sy\App::createConsole(require(TEST_APP . 'Config.php'));
