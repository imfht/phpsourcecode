<?php
	include_once "init.php";
	include_once "smarty_inc.php";
	include_once  WEB_ROOT."util/mysql_class.php";
	include_once  WEB_ROOT."util/util.php";
	include_once WEB_ROOT."admin/funcs/app_util_func.php";
	include_once  WEB_ROOT."util/Model_class.php";

	function getTopNovel(){
		$model = new Model();
		
		if(@$_GET["id"]){
			$id = verify_id($_GET["id"]);
			$category_id = " and c.id=".$id;
		}else{
			$category_id ="";
		}
		$sql_select="select a.*,c.category_name from t_article a,t_seeds b,t_category c where a.seed_id = b.id and b.category_id=c.id ".$category_id." order by a.click_times desc limit 0,30";
		
		$model->query($sql_select);
		
		return $model->fetch_all();
	}
	
	if(@$_POST["search_text"]){
		$title = trim($_POST["search_text"]);
		//$title = str_check($title);
		//mysql_real_escape_string($value)
		//echo $title;
		$db =  new mysql();
		$sql_select="select a.*,c.category_name  from t_article a,t_seeds b,t_category c  where a.title like '%".$title."%' and a.seed_id = b.id and b.category_id=c.id";
		$query = $db->query($sql_select);
		while($row=$db->fetch_row_array($query)){
			$arr[] = $row;
		}
		$links = getLinks();
		$advert = getadvert();
		$tops = getTopNovel();
		
		$smarty->assign("click_artile",$tops);
		$smarty->assign("links",$links);
		$smarty->assign("advert",$advert);
		$smarty->assign("artile",$arr);
		$smarty->display("common_header.htm");
		$smarty->display("index_bootstrap.htm");
		$smarty->display("right-nav.htm");
		$smarty->display("common_footer.htm");
	}else{
		echo "请输入文章标题";
	}
?>
