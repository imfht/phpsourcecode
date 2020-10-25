<?php
	include_once "../init.php";
	include_once "../util/mysql_class.php";
	include_once "../smarty_inc.php";
	
	$db =  new mysql();
	$sql_select="select a.*,c.category_name from t_article a,t_seeds b,t_category c where a.seed_id = b.id and b.category_id=c.id";
	$query = $db->query($sql_select);
	
		while($row=$db->fetch_row_array($query)){
			$arr[] = $row;
		}

	$smarty->assign("artile",$arr);
	$smarty->display("artlst.htm");
?>