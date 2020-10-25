<?php
/**
 * 入口文件
 */
$rootPath = dirname(dirname(__FILE__)) . "/root";
include_once $rootPath . "/common/helper.php";
include_once $rootPath . "/common/common.php";
include_once $rootPath . "/controller/common/Base.php";
include_once $rootPath . "/controller/common/BaseApi.php";
include_once $rootPath . "/module/Base.php";
App::run("api", $rootPath);
