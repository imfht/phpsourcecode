<?php

define("IN_CART", true);
define("SITEPATH", dirname(__FILE__));

$stage = "api";
require SITEPATH . "/init.php";

//获取model，action
$model = isset($_REQUEST['model']) ? trim($_REQUEST["model"]) : 'front';
$action = isset($_REQUEST['action']) ? trim($_REQUEST["action"]) : 'index';

if (!in_array($model, array("etao"))) {
    $model = "etao";
    $action = "index";
}

//执行程序
if (file_exists(STAGEPATH . "/{$model}.class.php")) {
    $classname = ucfirst($model);
    $class = new $classname($model, $action);
    if (method_exists($class, $action)) {
        $class->$action();
    }
    exit();
}