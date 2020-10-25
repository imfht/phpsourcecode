<?php
include_once "init.php";
include_once "util/mysql_class.php";
include_once "smarty_inc.php";
include_once WEB_ROOT."admin/funcs/app_util_func.php";
include_once WEB_ROOT."util/util.php";
include_once  WEB_ROOT."util/Model_class.php";
include_once 'util/mobile_redirect.php';

$db =  new mysql();
if(@$_GET["id"]){
	$category_id = verify_id($_GET["id"]);
	$category_sql = " and c.id=".$category_id;
	$pageNo=verify_id($_GET["page"]);
}else{
	$category_sql ="";
	$pageNo=1;
}


$sql_total="select count(1) from t_article a,t_seeds b,t_category c where a.seed_id = b.id and b.category_id=c.id ".$category_sql ;
$total = get_total_count($sql_total);
$category_name=get_category_name($category_id);

$smarty->assign("total",$total);
$smarty->assign("pageNo",$pageNo);

$maxPg = (int)($total/15);
if($maxPg*15<$total){
	$maxPg = $maxPg+1;
}
$pagination="";
/*if($pageNo==1&$pageNo<$maxPg){
	$pagination = "<li><a href=\"/category/".$category_id."/".($pageNo+1).".html\">Next</a></li>";
} else if($pageNo==$maxPg&$pageNo>=$maxPg&$pageNo>1){
	$pagination = "<li><a href=\"/category/".$category_id."/".($pageNo-1).".html\">Prev</a></li>";
} else if($pageNo<$maxPg){
	$pagination = "<li><a href=\"/category/".$category_id."/".($pageNo-1).".html\">Prev</a></li><li><a href=\"/category/".$category_id."/".($pageNo+1).".html\">Next</a></li>";
}*/

$pagination = getPageInfo($category_id,$pageNo,$maxPg);


$links = getLinks();
$advert = getadvert();
$smarty->assign("links",$links);
$smarty->assign("advert",$advert);

$recent = getCRecent($category_sql,$pageNo,"a.id");
$arr = getCRecent($category_sql,"1","a.click_times");
$smarty->assign("click_artile",$arr);

$smarty->assign("maxPg",$maxPg);
$smarty->assign("total",$total);
$smarty->assign("pagination",$pagination);
$smarty->assign("category_name",$category_name);
$smarty->assign("activeIdx",$category_id);
$smarty->assign("artile",$recent);
$smarty->display("category_bootstrap.htm");
$smarty->display("right-nav.htm");
$smarty->display("common_footer.htm");
?>