<?php
session_start(); //此示例中要使用session
require_once('t163_config.php');
require_once('t163.php');

$t163_t=isset($_SESSION['t163_t'])?$_SESSION['t163_t']:'';

//检查是否已登录
if($t163_t!=''){
	$t163=new t163PHP($t163_k, $t163_s, $t163_t);

	//获取登录用户信息
	$result=$t163->me();
	var_dump($result);

	/**
	//access token到期后使用refresh token刷新access token
	$result=$t163->access_token_refresh($_SESSION['t163_r']);
	var_dump($result);
	**/

	/**
	//发布微博
	$result=$t163->update('微博内容');
	var_dump($result);
	**/

	/**
	//微博列表，$id来自登录用户信息中的$result['id']
	$result=$t163->user_timeline($id);
	var_dump($result);
	**/

	/**
	//其他功能请根据官方文档自行添加
	//示例：获取登录用户信息
	$result=$t163->api('users/show', array(), 'GET');
	var_dump($result);
	**/

}else{
	//生成登录链接
	$t163=new t163PHP($t163_k, $t163_s);
	$login_url=$t163->login_url($callback_url);
	echo '<a href="',$login_url,'">点击进入授权页面</a>';
}
