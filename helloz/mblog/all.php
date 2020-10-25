<?php
	session_start();
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once('./config/smarty.config.php');
	include_once('./config/config.php');
	
	$con = $db->connect();
	$sql = "SELECT * FROM  `article` ORDER BY  `id` DESC";
	$query = $db->query($sql,$con);
	$id = $_GET['delete'];
	$d = "DELETE FROM `article` WHERE `id` = $id";
	
	if(isset($_SESSION['account'])) {
		$smarty->assign('edit',1);
	}
	
	while($result = $db->fetch_arr($query,MYSQL_ASSOC)) {
		$all[] = $result;
	}
	
	$smarty->assign('all',$all);
	$smarty->display('all.html');
?>