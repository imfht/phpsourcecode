<?php
error_reporting(1);
define ("APPLICATION_PATH", dirname(__FILE__) . "/app");
define ("VIEW_PATH", dirname(__FILE__) . "/app/views");
define ("STATIC_PATH", dirname(__FILE__) . "/static");

$application = new Yaf_Application("conf/app.ini");

$response = $application
	->bootstrap()
	->run();
?>
