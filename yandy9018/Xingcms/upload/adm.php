<?php
error_reporting(0);
define(APP_IN,__file__);
include('common.inc.php');
include(INC_DIR.'permission.func.php');
include(INC_DIR .'html.func.php');
include ('index/page.php');
$m = isset($_GET['m']) ?$_GET['m'] : 'main';
if (!is_admin_login()) $m = 'login';
if (!file_exists('admin/'.$m .'.php')) exit('error url');
include('admin/'.$m .'.php');
?>