<?php 
/** ***********************
 * 作者：卢逸 www.61php.com
 * 日期：2015/5/21
 * 作用：61php框架入口文件
 ** ***********************/
header("Content-type: text/html; charset=utf-8");
session_start();
if(!file_exists('./install/install.log')){
	header('Location:./install');
}
ini_set('display_errors', true);
ini_set("error_reporting",E_ERROR);
define("_SITE_ROOT",dirname(__FILE__));
require('include/include.php');
//版本验证与类初始化
verify::check($GVar->fget);
?>