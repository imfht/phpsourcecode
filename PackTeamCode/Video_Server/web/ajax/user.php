<?php
include ("../../config/config.php");
include ("../include/function.php");
if ($_GET['action']=="login"){
    if ($_POST['username']==$admin_username){
        if ($_POST['password']==$admin_password){
            $_SESSION['login_status']=1;
            $return['code']="201";
            $return['data']['message']="Login Success";
            echo json_encode($return);
            exit;
        }else{
            $return['code']="101";
            $return['data']['message']="Login Failed.Error Password";
            echo json_encode($return);
            exit;
        }
    }else{
        $return['code']="101";
        $return['data']['message']="Login Failed.Error Username";
        echo json_encode($return);
        exit;
    }
}