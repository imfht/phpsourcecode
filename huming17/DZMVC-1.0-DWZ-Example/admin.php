<?php
if(!file_exists('./config/config_global.php')){
    header('location: install/index.php');
}
require_once './dz_framework/init.php';
//var_dump($_G);
//DEBUG 接收对象 动作
$mod=isset($_REQUEST['mod']) ? $_REQUEST['mod']:'index';
$action=isset($_REQUEST['action']) ? $_REQUEST['action']:'main';
$do=isset($_REQUEST['do']) ? $_REQUEST['do']:'index';
$ext=isset($_REQUEST['ext']) ? $_REQUEST['ext']:'';

if($_G['user_id']){
	
	$uid=$_G['user_id'];
	//$userinfo=seach_user_info_byuid($uid);
	//$level=get_level_byroleid($userinfo['roleid']);
	//if($_G['user_access'][$mod][$action][$do]){
	require_once libfile('admin/'.$mod, 'module','..');
	//}else{
	//	header('Location: index.php');
	//}
}else{
	include template('admin/login');
}

?>