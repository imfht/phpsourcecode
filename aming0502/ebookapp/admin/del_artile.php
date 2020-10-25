<head>
    <meta charset="gb2312">
</head>	
<?php
	include_once "../init.php";
	include_once "../util/Model_class.php";
	include_once "../smarty_inc.php";
	include_once "../util/util.php";
	
	function del_article_func($artileid){
		$model = new Model();
		$model->delete("t_article", "id=".$artileid);
		$model->delete("t_chapter", "artile_id=".$artileid);
	}
	
	$artileid=$_GET["artileid"];
	del_article_func($artileid);
	echo "ok";
	
	
?>