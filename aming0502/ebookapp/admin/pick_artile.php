<head>
    <meta charset="gb2312">
</head>	
 <?php
	include_once "../init.php";
	include_once "../util/Model_class.php";
	include_once "../util/util.php";
	include_once "funcs/pick_artile_func.php";
	include_once "funcs/app_util_func.php";
	include_once "Parser_class.php";
	include_once "../third_part/simple_html_dom.php";
	
	$artileid="";
	
	if(@$_GET["artileid"]){
		$artileid=$_GET["artileid"];
	}else{
		$condition = "length( modify_date )<1";
		$model = new Model();
		$rows = $model->select("t_article","*",$condition);
		if($rows){
			$artile = $rows[0];
			$artileid = $artile["id"];
		}else{
			exit("no article pick");
		}
	}
	
	pick_article($artileid,true);
	//echo "content1:".myfile_get_content('http://www.81zw.com/book/7586/');
?>