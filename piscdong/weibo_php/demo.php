<?php
session_start(); //此示例中要使用session
require_once('config.php');
require_once('sina.php');

function getimgp($u){
	//图片处理
	$c=@file_get_contents($u);
	$name=md5($u).'.jpg';
	$mime='image/unknown';
	return array($mime, $name, $c);
}

$sina_t=isset($_SESSION['sina_t'])?$_SESSION['sina_t']:'';

//检查是否已登录
if($sina_t!=''){
	$sina=new sinaPHP($sina_k, $sina_s, $sina_t);

	//获取登录用户id
	$sina_uid=$sina->get_uid();
	$uid=$sina_uid['uid'];

	//获取登录用户信息
	$result=$sina->show_user_by_id($uid);
	var_dump($result);

	/**
	//发布微博
	$content='微博内容';
	$img='http://www.baidu.com/img/baidu_sylogo1.gif';
	$img_a=getimgp($img);
	if($img_a[2]!=''){
		$result=$sina->update($content, $img_a);
		//发布带图片微博
	}else{
		$result=$sina->update($content);
		//发布纯文字微博
	}
	var_dump($result);
	**/

	/**
	//微博列表
	$result=$sina->user_timeline($uid);
	var_dump($result);
	**/

	/**
	//其他功能请根据官方文档自行添加
	//示例：根据uid获取用户信息
	$result=$sina->api('users/show', array('uid'=>$uid), 'GET');
	var_dump($result);
	**/

}else{
	//生成登录链接
	$sina=new sinaPHP($sina_k, $sina_s);
	$login_url=$sina->login_url($callback_url);
	echo '<a href="',$login_url,'">点击进入授权页面</a>';
}
