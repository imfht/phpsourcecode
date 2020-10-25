<?php
/**
 * 微信登录页面
 *
 */

$wxid = isset($_GET['wxid']) ? $_GET['wxid'] : '';
//如果从get获取不到数据则尝试从post中获取
if($wxid==''){
	$wxid = isset($_POST['wxid']) ? $_POST['wxid'] : '';
}
//$username = isset($_GET['username']) ? $_GET['username'] : '';

if(!empty($wxid)){

	//  初始化数据 调用数据配置
	define('IN_ECS', true);
	require(dirname(__FILE__) . '/../includes/init.php');
	require(dirname(__FILE__) . '/../data/config.php');

	$query_sql = "SELECT `user_name` FROM  ".$ecs->table('users')." WHERE `wxid` = '$wxid'";
	$username = $db->getOne($query_sql);

	if(!empty($username)){

		$user->set_session($username);
		$user->set_cookie($username);
		update_user_info();
		recalculate_price();
	}
	$endurl = '?wch=1';

	$query_string = $_SERVER['QUERY_STRING'];
	$q_arr = explode('&',$query_string);
	unset($_GET['wxid']);
	unset($_GET['username']);
	foreach($_GET as $k=>$v)
	{
		$endurl .= '&'.$k.'='.$v;
	}

	$Loaction = 'http://'.$_SERVER['HTTP_HOST'].'/mobile/user.php'.$endurl;

	ecs_header("Location: $Loaction\n");
}