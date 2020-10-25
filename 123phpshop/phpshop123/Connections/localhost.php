
<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
error_reporting(0); 
$hostname_localhost = "";
$database_localhost = "";
$username_localhost = "";
$password_localhost = "";
$localhost = mysql_pconnect($hostname_localhost, $username_localhost, $password_localhost) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_query("set names utf8");

if (!isset($_SESSION)) {
  session_start();
}
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/check_install.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/const.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/lib/common.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/lib/cart.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/check_admin_login.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/check_user_login.php";
?>