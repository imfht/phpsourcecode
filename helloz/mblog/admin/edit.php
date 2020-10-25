<?php
	session_start(); 
	header("Content-Type: text/html; charset=UTF-8");
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$id = $_GET['id'];
	$title = $_POST['title'];
	$content = $_POST['content'];
	$keywords = $_POST['keywords'];
	$description = $_POST['description'];
	$db->power();		//判断用户是否登录
	$article = $db->article($id);		//使用GET获取ID输出单篇文章信息
	
	if($_POST['sub']) {
		$db->edit($title,$keywords,$description,$content,$id);
		echo "<script>window.location.href='./list.php';</script>";
	}
	
	
	$smarty->assign('article',$article);
	$smarty->display("edit.html");
?>