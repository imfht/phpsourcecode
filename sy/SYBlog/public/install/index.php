<?php

/**
 * 入口文件
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */
error_reporting(E_ALL &~ E_NOTICE);
define('APP_PATH', realpath(__DIR__ . '/../../application/') . '/');
if (file_exists(__DIR__ . '/install.lock')) {
	echo 'Please delete install/install.lock to unlock install program';
	exit;
}
require('install.class.php');
$action = $_GET['action'];
if (empty($action)) {
	$action = 'install';
}
$action = 'action' . ucfirst($action);
if (is_callable('Install::' . $action)) {
	call_user_func('Install::' . $action);
}
