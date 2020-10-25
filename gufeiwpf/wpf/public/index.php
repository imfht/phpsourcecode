<?php
ini_set("display_errors","on");
error_reporting(E_ALL & ~E_NOTICE);
define('APP_DEBUG',true);
ob_start();

require_once(realpath('../')."/core/Init.php");
$wpfInit = new \Wpf\Core\Init();
$wpfInit->run();
