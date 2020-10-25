<?php

defined("IN_CART") or die;

/**
 *
 * 解析sql文件
 *
 */
function parse_sqlfile($file)
{
    if (!file_exists($file))
        return false;

    $sql = file_get_contents($file);
    if (!$sql)
        return false;

    $sql = str_replace("\r", "", $sql);
    $sqlarr = explode(";\n", $sql);
    return $sqlarr;
}

/**
 *
 * 返回siteurl
 * 
 */
function getweburl()
{
    $uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);

    $uri = substr($uri, 0, strrpos($uri, 'install/'));

    return htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . $uri);
}

/**
 *
 * 创建数据库
 *
 */
function create_database($name)
{
    global $conn;
    //db
    if (mysqli_select_db($conn, $name) === false) {
        $sql = "CREATE DATABASE `$name` DEFAULT CHARACTER SET utf8";
        if (mysqli_query($conn, $sql) === false) {
            return false;
        }
        mysqli_select_db($conn, $name);
    }
    return true;
}

/**
 *
 * 导入sql文件
 *
 */
function import_sqlfile($prefix, $filename)
{
    global $conn;
    $sqlfile = INSTALLPATH . "/{$filename}.sql";
    if (!file_exists($sqlfile))
        return false;
    $sqlarr = parse_sqlfile($sqlfile);
    if (!$sqlarr)
        return false;

    foreach ($sqlarr as $sql) {
        if (strlen($sql) < 10)
            continue;
        $sql = str_replace("#__", $prefix, $sql);
        if (mysqli_query($conn, $sql) === false) {
            return false;
        }
    }
    return true;
}

function install_db($prefix)
{
    return import_sqlfile($prefix, "data/db");
}

/**
 *
 * 安装体验数据
 *
 */
function install_test($prefix)
{
    return import_sqlfile($prefix, "data/test");
}

/**
 *
 * 生成配置文件
 *
 */
function create_config($host, $port, $user, $pass, $name, $prefix, $driver)
{
    $file = INSTALLPATH . "/config.inc.sample.php";
    $newfile = SITEPATH . "/config.inc.php";
    $content = file_get_contents($file);
    $content = str_replace(
            array("__DBHOST__", "__DBPORT__", "__DBUSER__", "__DBPASS__", "__DBNAME__", "__DBPREFIX__", "__DBDRIVER__", "__WEBURL__"), array($host, $port, $user, $pass, $name, $prefix, $driver, getweburl()), $content);

    $length = file_put_contents($newfile, $content);
    return $length > 10;
}

/**
 *
 * 新建管理员
 *
 */
function create_admin($uname, $pass, $email, $prefix)
{
    global $conn;
    $passarr = encpass($pass);
    $sql = "INSERT INTO {$prefix}admin(uname,pass,salt,email,addtime,issuper) values('$uname','" . $passarr['pass'] . "','" . $passarr['salt'] . "','$email','" . time() . "',1)";
    return mysqli_query($conn, $sql);
}

/**
 *
 * 插入配置
 *
 */
function insert_config($mallname, $email, $adminfile, $prefix)
{
    global $conn;
    $sql = "REPLACE INTO {$prefix}config(`key`,`val`,`type`) values('mallname','$mallname','basicset'),('bossemail','$email','basicset'),('adminfile','$adminfile','basicset')";
    if ($adminfile && $adminfile != 'admin' && !file_exists(SITEPATH . '/' . $adminfile . '.php') && file_exists(SITEPATH . '/admin.php')) {
        if (!@rename(SITEPATH . '/admin.php', SITEPATH . '/' . $adminfile . '.php')) {//有可能出现重装的情况
            return false;
        }
    }
    return mysqli_query($conn, $sql);
}

/**
 *
 * 生成lock文件
 *
 */
function create_lock()
{
    $file = DATADIR . "/lock/install.lock";
    return @touch($file);
}
