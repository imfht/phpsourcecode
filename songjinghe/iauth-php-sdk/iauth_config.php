<?php
/* ############### CLIENT ( WSC and UAC ) COMMON CONFIG ############## */
/* 以下是WSC和UAC都需要用到的通用配置项，如果您开发UAC或WSC，必须配置以下几项 */
define("IAUTH_APP_ID","cab4d4effedabf32");
define("IAUTH_APP_SECRET","e8e6954f571a757cf836164f8c897f4a");
define("IAUTH_TIME_OFFSET",0);
define("IAUTH_ACCESS_URL","http://i.buaa.edu.cn/plugin/iauth/access.php");

/* ############### WSC ( web site client ) CONFIG ############## */
/* 以下这一项是WSC需要用到的专用配置项，如果您开发WSC，必须配置以下这项 */
//define("IAUTH_IP_CHECK","ON"); //如果您的应用仅允许在校内使用，请ON（推荐）
define("IAUTH_IP_CHECK","OFF"); //如果您的应用允许在校外使用，或者您在本地环境调试，请使用OFF

//define("IAUTH_INNER_NET",TRUE); //如果您的应用与ihome在同一内网网段，请将该项设为TRUE。
define("IAUTH_INNER_NET",FALSE); //大多数情况FALSE即可。

define("IAUTH_GETUID_URL","http://i.buaa.edu.cn/plugin/iauth/getuid.php");
define("IAUTH_LOGIN_URL","http://i.buaa.edu.cn/plugin/iauth/login.php");


/* ############### debug ############## */
ini_set('display_errors',1);
error_reporting(E_ALL);

?>
