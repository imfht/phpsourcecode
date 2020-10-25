<?php

//安装
define("IN_CART", true);
define("C_VER", "1.0"); //定义版本
define("C_RELEASE", "20121219");
define("SITEPATH", dirname(dirname(__FILE__)));
define("INSTALLPATH", SITEPATH . "/install");
define("COMMONPATH", SITEPATH . "/include/common");
define("DATADIR", SITEPATH . "/data");

//设置
date_default_timezone_set("Asia/Shanghai");
@ini_set("memory_limit", '64M');
@ini_set('session.cache_expire', 180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies', 1);
@ini_set('session.auto_start', 0);

//加载公用
require COMMONPATH . "/global.function.php";

//转义
if (!get_magic_quotes_gpc()) {
    if (!empty($_GET)) {
        $_GET = caddslashes($_GET);
    }
    if (!empty($_POST)) {
        $_POST = caddslashes($_POST);
    }
    $_COOKIE = caddslashes($_COOKIE);
    $_REQUEST = caddslashes($_REQUEST);
}

require_once INSTALLPATH . "/function.php";
require_once INSTALLPATH . "/lang/message.php";


$step = isset($_REQUEST["step"]) ? intval($_REQUEST["step"]) : 1;
$errors = array();

if ($step > 4 || $step < 1 || ($step > 1 && !ispostreq())) {
    $step = 0;
    $errors = __("ACCESS_ERROR");
}
$insret = false;

//生成安装lock文件
if (file_exists(DATADIR . "/lock/install.lock") && ($step != 4)) {
    $errors = __("LOCK_EXIST");
    $step = 0;
} else {
    //安装步骤
    switch ($step) {
        case 2:
            //验证
            if (PHP_VERSION < '5.2') {    //php 版本过低
                $errors[] = __('PHP_VERSION_OLDER');
            }

            if (!@ini_get("file_uploads")) {   //文件上传
                $errors[] = __("FILE_UPLOAD_UNSUPPORT");
            }

            if (!extension_loaded('pdo')) {  //php，db扩展
                $errors[] = __("PHP_EXTENTION_UNLOAD_MYSQL");
            }

            if (!extension_loaded("gd")) {   //gd
                $errors[] = __("PHP_EXTENTION_UNLOAD_GD");
            }

            if (!extension_loaded("mbstring")) {  //mbstring
                $errors[] = __("PHP_EXTENTION_UNLOAD_MBSTRING");
            }

            //设置data目录的写权限
            setMod(DATADIR);
            //判断可写
            if (!is_writable(DATADIR)) {
                $errors[] = __("DATA_DIR_NOT_WRITABLE");
            }

            setMod(SITEPATH . "/uploads");
            if (!is_writable(SITEPATH . "/uploads")) {
                $errors[] = __("UPLOADS_DIR_NOT_WRITABLE");
            }
            if (!is_writable("../")) {
                $errors[] = __("ROOT_NOT_WRITABLE");
            }
            break;
        case 3://导入sql

            $dbhost = !empty($_POST["dbhost"]) ? trim($_POST["dbhost"]) : "localhost";
            $dbport = !empty($_POST["dbport"]) ? trim($_POST["dbport"]) : "3306";
            $driver = trim($_POST["driver"]);

            $dbuser = trim($_POST["dbuser"]);
            $dbpass = trim($_POST["dbpass"]);
            $dbname = trim($_POST["dbname"]);
            $dbprefix = trim($_POST["dbprefix"]);

            $mallname = trim($_POST["mallname"]);
            $uname = trim($_POST["adminname"]);
            $pass = trim($_POST["adminpass"]);
            $pass2 = trim($_POST["adminpass2"]);
            $email = trim($_POST["email"]);

            $adminfile = trim($_POST["adminfile"]);
            !$adminfile && $adminfile = 'admin';

            $test = isset($_POST["test"]) ? true : false;
            $conn = false;

            if ($driver == "pdo") {
                if (!extension_loaded("pdo")) {
                    $errors = __("PHP_EXTENTION_UNLOAD_PDO");
                    break;
                }
                if (!extension_loaded("pdo_mysql")) {
                    $errors = __("PHP_EXTENTION_UNLOAD_PDOMYSQL");
                    break;
                }
            }
            //创建数据库
            $conn = mysqli_connect($dbhost . ":" . $dbport, $dbuser, $dbpass);
            if (!$conn) {
                $errors = __("CANNT_CONNECT_MYSQL_HOST");
                break;
            }
            mysqli_query($conn, "SET names utf8");

            //判断mysql版本
            $mysql_ver = mysqli_get_server_info($conn);
            if ($mysql_ver < '5.0') {
                $errors = __("MYSQL_VERSION_OLDER");
                break;
            }

            if (!create_database($dbname)) {
                $errors = __("CANNT_CREATE_DATABASE");
                break;
            };

            //导入sql文件
            if (!install_db($dbprefix)) {
                $errors = __("IMPORT_SQLFILE_ERROR");
                break;
            }

            //插入配置文件
            if (!insert_config($mallname, $email, $adminfile, $dbprefix)) {
                $errors = __("ADD_DBCONFIG_ERROR");
                break;
            }

            //新建管理员
            if (!create_admin($uname, $pass, $email, $dbprefix)) {
                $errors = __("ADD_ADMIN_ERROR");
                break;
            }

            //生成配置文件
            if (!create_config($dbhost, $dbport, $dbuser, $dbpass, $dbname, $dbprefix, $driver)) {
                $errors = __("CREATE_CONFIGFILE_ERROR");
                break;
            }

            //是否安装体验数据
            if ($test && !install_test($dbprefix)) {
                $errors = __("INSTALL_TEST_ERROR");
                break;
            }

            //生成lock文件
            if (!create_lock()) {
                $errors = __("TOUCH_LOCK_ERROR");
            }
            $insret = true;
            break;
        case '4'://删除安装目录
            exit(deldir(INSTALLPATH) ? "success" : "failure");
            break;
    }
}

require 'view/index.php';
