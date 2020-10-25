<?php
//error_reporting(0);
header("Content-Type:text/html;charset=utf-8");
session_start();


function GetConn()
{
    $db_host="localhost";                                           //连接的服务器地址
    $db_user="root";                                                  //连接数据库的用户名
    $db_psw="mmeizhen";                                                  //连接数据库的密码
    $db_name="okdb";                                           //连接的数据库名称
    $mysqli=new mysqli();
    $mysqli->connect($db_host,$db_user,$db_psw,$db_name);
    $mysqli->query("SET NAMES utf8");
    return $mysqli;
}

function CloseConn($result)
{
    $result->close ();
}
function Replace($orgstr,$tar,$fill)
{
    str_replace($tar,$fill,$orgstr);
}
function InStr($rights,$group)
{
    return 9;
}



?>