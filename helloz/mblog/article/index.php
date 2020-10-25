<?php
	session_start();
	error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED);
	include_once(dirname(dirname(__FILE__)).'/config/smarty.config.php');
	include_once(dirname(dirname(__FILE__)).'/config/config.php');
	
	$id = intval($_GET['p']);			//获取当前页ID
	$cat = $_SERVER['PHP_SELF'];
	$cat = str_replace('index.php','',$cat);
	$url = 'http://'.$_SERVER['HTTP_HOST'].$cat.'?'.$_SERVER['QUERY_STRING'];
	$sql = "SELECT `id`, `date`, `title`, `keywords`, `description`, `content` FROM `article` WHERE `id`= $id";
	$seo = $db->seo();		//获取SEO信息
	
	/* $con = $db->connect();
	$query = $db->query($sql,$con);
	$result = $db->fetch_arr($query); */
	$result = $db->article($_GET['p']);  //从对象中调用文章函数
	//print_r($result);
	/* $rows = $db->rows($query);
	
	//print_r($result);
	if($rows == 0) {
		echo "404您访问的页面不存在";
		exit();
		return false;
	} */
	if($result == "") {
		$smarty->display('404.html');
		echo "<meta http-equiv=\"refresh\" content=\"5; url=../index.php\" />";
		exit();
		return false;
	}
	if(!isset($_GET['p'])) {
		$smarty->display('404.html');
		echo "<meta http-equiv=\"refresh\" content=\"5; url=../index.php\" />";
		exit();
		return false;
	}
	if(isset($_SESSION['account'])) {
		$smarty->assign('edit',1);
	}
	
	$smarty->assign('seo',$seo);
	$smarty->assign("url",$url);
	$smarty->assign("result",$result);
	$smarty->display('article.html');
?>