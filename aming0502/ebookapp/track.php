<?php
	include_once "init.php";
	include_once "smarty_inc.php";
	include_once  WEB_ROOT."util/mysql_class.php";

	if(@$_GET["articleid"]){
		$articleid = $_GET["articleid"];
		$db =  new mysql();
		$update_sql="update t_article set click_times = click_times+1  where id=".$articleid;
		$db->query($update_sql);
	}
?>
