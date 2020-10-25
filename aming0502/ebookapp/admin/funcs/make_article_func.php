<?php
	function make_article_func($artileid,$debug){
		global $smarty;
		$db =  new mysql();
		$sql_select="select a.*,b.category_id from t_article a,t_seeds b where a.id = ".$artileid." and a.seed_id = b.id";
		//echo $sql_select;
		$query = $db->query($sql_select);
		$titlerow = $db->fetch_row_array($query);
		$artile = $titlerow["title"];
		$category_id = $titlerow["category_id"];
		if($debug){
			echo "title:".$artile."<br>";
		}
		$sql_select="select * from t_chapter where artile_id=".$artileid." order by id ";
		$query = $db->query($sql_select);
		
		while($row=$db->fetch_row_array($query)){
			$row["local_url"]="data/chapters/".$row["id"].".htm";
			$update_sql="update t_chapter set local_url='".$row["local_url"]."' where id=".$row["id"];
			$db->query($update_sql);
			$arr[] = $row;
		}
		
		$links = getLinks();
		$advert = getadvert();
		$smarty->assign("links",$links);
		$smarty->assign("advert",$advert);
		$smarty->assign("title",$artile);
		$smarty->assign("chapter",$arr);
		$smarty->assign("activeIdx",$category_id);
		
		$category_sql = " and c.id=".$category_id;
		$topClicks = getCRecent($category_sql,"1","a.click_times");
		$smarty->assign("click_artile",$topClicks);
		
		//$smarty->display("chplst.htm");
		$content_body = $smarty->fetch('chplst_bootstrap_1.htm');
		$content_right = $smarty->fetch("right-nav.htm");
		$content_footer = $smarty->fetch('common_footer.htm');
		$local_url="data/artiles/".$artileid.".htm";
		
		$update_sql="update t_article set local_url='".$local_url."' where id=".$artileid;
		$db->query($update_sql);
		
		makehtml(WEB_ROOT.$local_url,$content_body.$content_right.$content_footer);
		
		if($debug){
			echo "<a href='".$GLOBALS ['web'] ['base'].$local_url."'>生成文章目录页成功</a>";
		}
	}
	
	function get_article_title($artileid){
		$db =  new mysql();
		$sql_select="select * from t_article where id = ".$artileid;
		$query = $db->query($sql_select);
		$titlerow = $db->fetch_row_array($query);
		$artile = $titlerow["title"];
		return $artile;
	}
?>