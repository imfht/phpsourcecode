<?php
require_once("../Conn.php");
require_once("clsAdmin_Info.php");
require "global.func.php";
require "../include/post.func.php";
$_admin = get_cookie($secretkey);
$_admin = $_admin ? intval($_admin) : 0;
if (!$_admin) msg('', '/admin/ad_login.php?file=login&forward=' . urlencode($DT_URL));

$DT_REF = get_env('referer');
$forward = isset($forward) ? urldecode($forward) : $DT_REF; strip_uri($forward);
$action = (isset($action) && check_name($action)) ? trim($action) : '';
$submit = isset($_POST['submit']) ? 1 : 0;

/*
if (!admin_check())
{
    admin_log(1);
    $db->query("DELETE FROM {$db->pre}admin WHERE userid=$_userid AND url='?" . $DT_QST . "'");
    msg('警告！您无权进行此操作 Error(00)');
}*/
