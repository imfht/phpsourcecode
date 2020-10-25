<?php
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once('./config/smarty.config.php');
	//include_once('./header.php');
	include_once('./config/config.php');
	
	$con = $db->connect();
	$sql = "SELECT * FROM `article` ORDER BY `id` DESC";
	$show = "SELECT * FROM `article` ORDER BY `id` DESC limit 0,10";
	$query = $db->query($sql,$con);
	$pquery = $db->query($show,$con);
	$rows = $db->rows($query);		//总条数
	//$page = $rows / 10 + 1;				//页数
	//$page = floor($page);
	$seo = $db->seo();
	
	//if()
	if(!isset($_GET['p'])) {
		while($result = $db->fetch_arr($pquery,MYSQL_ASSOC)) {
			$arr[] = $result;//将结果转换为二维数组
			$smarty->assign('up','#');
			$smarty->assign('dn',2);
	}
	}
	
	if(isset($_GET['p'])) {
		$page = intval($_GET['p']);
		$start = $page * 10 - 10;
		$end = $page * 10;
		if($page <= 0) {
			$start = $page * 0;
			$end = 10;
			echo "<script>window.location.href='./index.php';</script>";
		}
	
		$new = "SELECT * FROM `article` ORDER BY `id` DESC limit $start,$end";
		$newquery = $db->query($new,$con);
		$rows = $db->rows($newquery);
		if($rows == 0) {
			$smarty->display('404.html');
			echo "<meta http-equiv=\"refresh\" content=\"5; url=../index.php\" />";
			exit();
			return false;
		}
		if($page >= 2) {
			$smarty->assign('ptitle'," | 第".$page."页");
		}
		while($result = $db->fetch_arr($newquery,MYSQL_ASSOC)) {
			$arr[] = $result;//将结果转换为二维数组
		}
		$smarty->assign('up',$page-1);
		$smarty->assign('dn',$page+1);
	}
	
	
	$smarty->assign('seo',$seo);
	$smarty->assign("arr",$arr); 
	$smarty->display("index.html");
?>