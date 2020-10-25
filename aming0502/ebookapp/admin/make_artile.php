<head>
    <meta charset="gb2312">
</head>	
<?php
	include_once "../init.php";
	include_once "../util/mysql_class.php";
	include_once "../smarty_inc.php";
	include_once "../util/util.php";
	include_once "funcs/make_article_func.php";
	include_once "funcs/app_util_func.php";
	
	$artileid=$_GET["artileid"];
	make_article_func($artileid,true);
	
?>