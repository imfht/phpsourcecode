<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
session_start(); //初始session
error_reporting(E_ALL ^ E_NOTICE);
include_once $_SERVER['DOCUMENT_ROOT'] . '/Index/Action/Index_Config_Action.php';
$administrator = $Mark_Config_Action['user_name'];
$password = $Mark_Config_Action['user_pass'];
if (isset($_SESSION['Mark_Login'])) { //判断SESSINON中是否有登陆
    header("Location:/Root/index.php"); //重新定向到其他页面
    exit;
}
$root = $_POST['username'];
$pass = MD5($_POST['password']);
if ($root== '') { //判断POST来的用户名是否为空
    echo "<script language=javascript>alert('Please,Check Your Username and Password!');window.location='/Root/Blog_System_Login.php'</script>";
} elseif ($pass == '') { //判断POST来的密码名是否为空
    echo "<script language=javascript>alert('Please,Check Your Username and Password!');window.location='/Root/Blog_System_Login.php'</script>";
} else { //两者不为空是判断用户名与密码是否正确
    if ($root == $administrator && $password == $pass) {
        $_SESSION["Mark_Login"] = "Mark_Login"; //注册新的变量,保存当前会话的昵称
        header("Location:/Root/index.php"); //登录成功重定向到管理页面
        
    } else {
        echo "<script language=javascript>alert('Please,Check Your Password!');window.location='/Root/Blog_System_Login.php'</script>";
    }
}
?>