<?php
define('ROOT_PATH', realpath(__DIR__.'/../').'/');
define('BOOT_PATH', ROOT_PATH.'bootstrap/');

define('APP_PATH', ROOT_PATH.'application/');

define('EXTEND_PATH', ROOT_PATH.'extend/');
define('PUBLIC_PATH', ROOT_PATH.'public/');
define('RUNTIME_PATH', ROOT_PATH.'runtime/');
define('TEMPLATE_PATH', ROOT_PATH.'template/');

require BOOT_PATH.'bootstrap.php';