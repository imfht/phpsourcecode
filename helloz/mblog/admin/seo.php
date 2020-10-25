<?php
	session_start(); 
	header("Content-Type: text/html; charset=UTF-8");
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$db->power();
	$title = $_POST['title'];
	$subtitle = $_POST['subtitle'];
	$keywords = $_POST['keywords'];
	$description = $_POST['description'];
	$con = $db->connect();
	$sql = "SELECT * FROM seo";
	$query = $db->query($sql,$con);
	$rows = $db->rows($query);
	//echo $rows;
	if($_POST['save']) {
		if($rows == 0) {
			$sql = "INSERT INTO `seo`(`title`, `keywords`, `description`, `subtitle`) VALUES ('$title','$keywords','$description','$subtitle')";
			$query = $db->query($sql,$con);
			echo "<script>alert('插入成功！');</script>";
			echo "<script>window.history.go(0);</script>";
		}
		if($rows == 1) {
			$sql = "UPDATE `seo` SET `title`='$title',`keywords`='$keywords',`description`='$description',`subtitle`='$subtitle' WHERE 1";
			$query = $db->query($sql,$con);
			echo "<script>alert('更新成功！');</script>";
			echo "<script>window.history.go(0);</script>";
		}
	}
	
	$result = $db->fetch_arr($query);
	
	$smarty->assign('result',$result);
	$smarty->display('seo.html');
?>