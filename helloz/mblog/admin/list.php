<?php
	session_start(); 
	header("Content-Type: text/html; charset=UTF-8");
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$db->power();
	$con = $db->connect();
	$sql = "SELECT * FROM  `article` ORDER BY  `id` DESC";
	$query = $db->query($sql,$con);
	$id = $_GET['delete'];
	$d = "DELETE FROM `article` WHERE `id` = $id";
	
	while($result = $db->fetch_arr($query,MYSQL_ASSOC)) {
		$list[] = $result;
	}
	
	if(isset($_GET['delete'])) {
		$db->query($d,$con);
		echo "<script>alert('删除成功！');</script>";
		echo "<script>window.location.href='?update';</script>";
	}
	//print_r($list);
	$smarty->assign('list',$list);
	$smarty->display('list.html');
?>