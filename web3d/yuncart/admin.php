<?php

define("IN_CART", true);
define("SITEPATH", dirname(__FILE__));

$stage = "admin";
require SITEPATH . "/init.php";
$model = isset($_REQUEST['model']) ? trim($_REQUEST["model"]) : 'dashboard';
$action = isset($_REQUEST['action']) ? trim($_REQUEST["action"]) : 'index';
$token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : '';

if (empty($_SESSION["admin"]["adminid"]) && $model != "login") { //未登录
    $auth = trim(cgetcookie('auth'));
    $adminid = intval(cgetcookie('adminid'));
    if (!empty($auth) && !empty($adminid)) { //如果存在cookie
        $admin = DB::getDB()->selectrow("admin", "adminid,uname,pass,salt,role,issuper", "adminid='$adminid'");
        if (!empty($admin) && $auth == md5($admin['salt'] . $admin['pass'])) { //如果cookie合法
            $_SESSION['admin'] = $admin;
            $_SESSION['admin']['token'] = md5(mt_rand());
            //更新用户最后登录时间
            DB::getDB()->update("admin", array("lasttime" => time()), "adminid='$adminid'");

            //权限
            $_SESSION['admin']['privs'] = array();
            if (!$admin['issuper'] && $admin['role']) {
                $tmppriv = DB::getDB()->selectcol("role", "privilege", "roleid in (" . $admin['role'] . ")");
                if ($tmppriv) {
                    foreach ($tmppriv as $val) {
                        $_SESSION['admin']['privs'] = array_merge($_SESSION['admin']['privs'], explode(",", $val));
                    }
                }
            }
            redirect(url('admin', 'dashboard'));
        } else {
            csetcookie('auth', '', 1);
            csetcookie('adminid', '', 1);
        }
    }
    redirect(url('admin', 'login'));
} else { //已经登录
    //pagemodel
    $pagemodel = "";

    if (!empty($_SESSION['admin']['token']) && $token != $_SESSION['admin']['token']) {
        unset($_SESSION['admin']);
        cerror(__("token_invalid"), url('admin', 'login'));
    }
}

if (file_exists(STAGEPATH . "/{$model}.class.php")) {
    $classname = ucfirst($model);
    $class = new $classname($model, $action);
    if (method_exists($class, $action)) {
        $class->$action();
    }
    exit();
}



