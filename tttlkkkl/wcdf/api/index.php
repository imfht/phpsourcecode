<?php
define('APPLICATION_PATH', dirname(__FILE__));

ini_set('always_populate_raw_post_data', -1);
$HTTP_RAW_POST_DATA = file_get_contents('php://input');

$application = new Yaf\Application(APPLICATION_PATH . "/conf/application.ini");
header("Content-Type:text/html;charset=UTF-8");
error_reporting(E_ALL);
$application->bootstrap()->run();
//$application->getDispatcher()->dispath(new Yaf\Request_Simple());
?>
