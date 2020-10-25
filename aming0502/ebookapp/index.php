<?php
	include_once "init.php";
	include_once "smarty_inc.php";
	include_once  WEB_ROOT."util/Model_class.php";
	include_once  WEB_ROOT."util/util.php";
	include_once WEB_ROOT."admin/funcs/app_util_func.php";
	include_once 'util/mobile_redirect.php';
	
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
	
	
	
	
	
	$arr = getTopNovel();
	
	
	
	
	//print_r();
	$links = getLinks();
	$advert = getadvert();
	$smarty->assign("links",$links);
	$smarty->assign("advert",$advert);
	
	$recent = getRecent();
	$smarty->assign("click_artile",$arr);
	
	$smarty->assign("hotarticle",$arr[0]["title"]);
	$smarty->assign("artile",$recent);
	$smarty->display("common_header.htm");
	
	$history = "";
	if(@$_COOKIE["history"]){
		$history = $_COOKIE["history"];
	}
	$smarty->assign("history",$history);
	
	$smarty->display("index_bootstrap.htm");
	$smarty->display("right-nav.htm");
	$smarty->display("common_footer.htm");
?>