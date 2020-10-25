<?php

defined('IN_CART') OR die;
define("C_VER", "1.0"); //定义版本
define("C_RELEASE", "20121219");

if (defined("DEBUG")) {//代码级别，如果是开发调试阶段，开启这个参数
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
} else {
    ini_set("display_errors", 0);
    error_reporting(0);
}
define("COMMONPATH", SITEPATH . "/include/common");
define("DATADIR", SITEPATH . "/data");
define("CACHEDIR", DATADIR . "/filecache");

if (isset($stage)) {
    if ($stage != "api") {
        define("DWOOCACHE", DATADIR . "/dwoocache/" . $stage);
        define("DWOOCOMPILED", DATADIR . "/dwoocompiled/" . $stage);
        define("TPL", SITEPATH . "/template/" . $stage);
    }
    define("STAGEPATH", SITEPATH . "/include/" . $stage);
}

function loader($classname)
{
    if (defined("STAGEPATH")) {
        $file = STAGEPATH . "/" . strtolower($classname) . ".class.php";
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    $file = COMMONPATH . "/" . strtolower($classname) . ".class.php";
    if (file_exists($file)) {
        require $file;
        return;
    }
}

spl_autoload_register('loader');

define("THIRDPATH", SITEPATH . "/include/third");
define("LANGDIR", SITEPATH . "/language");
define("CRLF", "\r\n");

//设置
date_default_timezone_set("Asia/Shanghai");
@ini_set("memory_limit", '64M');
@ini_set('session.cache_expire', 180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies', 1);
@ini_set('session.auto_start', 0);

if (!file_exists(SITEPATH . "/config.inc.php") || filesize(SITEPATH . "/config.inc.php") < 10) {
    header("Location:install/index.php");
    exit();
}

//加载公用
require COMMONPATH . "/db.class.php";
require COMMONPATH . "/global.function.php";
require SITEPATH . "/config.inc.php";

//加载语言包
require LANGDIR . "/common_lang_message.php";
if (isset($stage))
    require LANGDIR . "/" . $stage . "_lang_message.php";


//转义
if (!get_magic_quotes_gpc()) {
    if (!empty($_GET)) {
        $_GET = caddslashes($_GET);
    }
    if (!empty($_POST)) {
        $_POST = caddslashes($_POST);
    }
}

//数据库
include COMMONPATH . "/session.class.php";
$sess = new Session();

//gzip
if (function_exists("ob_gzhandler") && intval(getConfig("gzip", 0))) {
    ob_start("ob_gzhandler");
} else {
    ob_start();
}

//session

function getConfig($key, $default = "", $serialize = false)
{
    static $configs = array();
    if (!$configs) {
        $configs = DB::getDB()->selectkv("config", "key", "val");
        global $_CONF;
        $configs += $_CONF;
    }
    return isset($configs[$key]) ? ($serialize ? unserialize($configs[$key]) : $configs[$key]) : $default;
}
