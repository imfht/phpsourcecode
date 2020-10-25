<?php
/**
 *  index.php 入口
 *
 * @copyright			(C) 2015-2030 YANG QQ182860914
 * @license				
 */
error_reporting(E_ERROR);
define('RUN_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
if(@$_GET['m']==''){
    //exit();
	header("location:/?m=admin&c=webctrl&a=login");
}

include RUN_PATH.'source/base.php';

pc_base::creat_app();