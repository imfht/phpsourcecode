<?php
define('LONGPHP_VERSION', '1.0.0');
define('DIR', strtr(dirname(dirname(__FILE__)), array('\\'=>'/')).'/');
define('DIR_CONTROLLER', DIR.'controller/');
define('DIR_CLASS', DIR.'class/');
define('DIR_CONF', DIR.'conf/');
define('DIR_FUN', DIR.'fun/');
define('DIR_LIB', DIR.'lib/');
define('DIR_TPL', DIR.'tpl/');
define('DIR_MODEL', DIR.'model/');

$global_mysql_object = '';

require_once DIR_LIB.'Libs.lib.php';
require_once DIR_CLASS.'Request.class.php';
require_once DIR_CLASS.'Router.class.php';
require_once DIR_FUN.'Source.fun.php';

switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR
}

$key = 'jfaawiaw;sadhawkjaw12@3SAWDasd!';

if (PHP_SAPI === 'cli'){
	$uri = $argv[1];
}else {
	$uri = $_SERVER['REQUEST_URI'];
}

$router = new Router();
$router->run();
