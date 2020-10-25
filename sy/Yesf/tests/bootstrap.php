<?php
use Yesf\Yesf;
use Yesf\Event\Internal;
define('PROJECT_PATH', realpath(__DIR__ . '/..') . '/');
define('APP_PATH', __DIR__ . '/TestApp/');
define('TEST_SRC', __DIR__ . '/Cases/');
define('TEST_APP', __DIR__ . '/TestApp/');
define('YESF_PATH', PROJECT_PATH . 'src/');
define('YESF_UNIT', 1);
define('YESF_TEST', __DIR__ . '/');
require(PROJECT_PATH . '/vendor/autoload.php');

$app = new Yesf();
$app->setEnvConfig(APP_PATH . 'Config/env.ini');

Internal::onWorkerStart();