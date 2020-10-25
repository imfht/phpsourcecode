<?php

if (!defined('APP_IN')) exit('Access Denied');
$arr_admin_type = array('administrator'=>'超级管理员','admin'=>'管理员');
$data = $db->row_select_one('admin',"adminid=".$_SESSION['ADMIN_UID']);
$data['admintype'] = $arr_admin_type[$data['admin_type']];
$data['last_login_time'] = date('Y-m-d H:i:s',$data['last_login_time']);
$tpl ->assign('admin',$data);
$pagecount = $db ->row_count('cars');
$tpl ->assign('pagecount',$pagecount);
if($settings['version']==2 or $settings['version']==3){
$dealercount = $db ->row_count('cars'," uid in (select id from ".$db ->tb_prefix ."member where ischeck=1 and isdealer=2)");
$tpl ->assign('dealercount',$dealercount);
$personcount = $db ->row_count('cars'," uid in (select id from ".$db ->tb_prefix ."member where ischeck=1 and isdealer=1)");
$tpl ->assign('personcount',$personcount);
}
$visitorcount = $db ->row_count('cars'," uid=0 ");
$tpl ->assign('visitorcount',$visitorcount);
$unauditedcount = $db ->row_count('cars',"isshow=0");
$tpl ->assign('unauditedcount',$unauditedcount);
$issellcount = $db ->row_count('cars',"issell=1");
$tpl ->assign('issellcount',$issellcount);
$nosellcount = $db ->row_count('cars',"issell=0");
$tpl ->assign('nosellcount',$nosellcount);
$select_brand = select_make('',$commoncache['markbrandlist'],'请选择品牌');
$tpl ->assign('select_brand',$select_brand);

$sysinfo = array();
$sysinfo['system'] = PHP_OS;
$sysinfo['software'] = $_SERVER['SERVER_SOFTWARE'];
$sysinfo['mysql'] = mysql_get_server_info();
$tpl ->assign('sysinfo',$sysinfo);
$tpl ->display('admin/index.html');

?>