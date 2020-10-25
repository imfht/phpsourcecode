<?php
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	session_start(); 
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$title = $_POST['title'];
	$content = $_POST['content'];
	$keywords = $_POST['keywords'];
	$description = $_POST['description'];
	$db->power();
	$sql = "INSERT INTO `article`(`date`, `title`, `keywords`, `description`, `content`) VALUES (NOW(),'$title','$keywords','$description','$content')";
	
	$con = $db->connect();
	
	if($_POST['sub']) {
		if($title == "") {
			echo "<script>alert(\"标题不能为空哦！\");</script>";
			echo "<script>window.history.go(-1);</script>";
		}
		if($content == "") {
			echo "<script>alert(\"内容不能为空哦！\");</script>";
			echo "<script>window.history.go(-1);</script>";
		}
		else {
			$db->query($sql,$con);
			echo "<script>alert('发表成功！');</script>";
			echo "<script>window.history.go(-1);</script>";
		}
	}
	
	
	$smarty->display('publish.html');
?>