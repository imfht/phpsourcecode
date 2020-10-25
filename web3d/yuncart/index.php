<?php

define("IN_CART", true);
define("SITEPATH", dirname(__FILE__));

$stage = "front";
require SITEPATH . "/init.php";

//获取model，action
$model = isset($_REQUEST['model']) ? trim($_REQUEST["model"]) : 'front';
$action = isset($_REQUEST['action']) ? trim($_REQUEST["action"]) : 'index';


if (!in_array($model, array("buy", "cart", "favor", "content", "front", "member", "item", "myservice", "myaddress", "mytrade", "order", "user", "listing", "search", "meal", "coupon", "mycoupon", "aftersale", "payment", "link"))) {
    $model = "front";
    $action = "index";
}

//商品短地址
if (!isset($_REQUEST['model']) && !empty($_GET)) {
    $gets = $_GET;
    if (count($gets) == 1 && is_numeric(key($gets))) {
        $model = "item";
        $action = "index";
        $_GET["itemid"] = key($gets);
    }
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