<?php
	include_once WEB_ROOT."util/util.php";
	
	function make_chapter_func($id,$debug){
		//echo "bb";
		$db =  new mysql();
		$sql_select="select * from t_chapter where  id=".$id;
		$query = $db->query($sql_select);
		$count = 0;
		
		while($row=$db->fetch_row_array($query)){
		
			if($debug){
				echo "开始采集 ".$row["url"]."->".$row["local_url"]."<br>";
			}
			
			makechaptercontent($id,$row["url"],$row["local_url"],$row["title"],$row["artile_id"]);
			
			if($debug){
				echo "采集".$row["url"]."完成<br>";
			}
			
			$update_sql="update t_chapter set collect_flag=1 where id=".$row["id"];
			$db->query($update_sql);
			$count++;
			//myflush();
		};
		return $count;
	}
	
	
	function makechaptercontent($id,$url,$localurl,$title,$artileid){
		global $smarty;
		$article_info = get_article_info($artileid);
		$parse_class = $article_info["parse_class"];
		$parserBean = new $parse_class();
		$chpctx = $parserBean->parse_level3($url);
		
		if(strlen($chpctx)<10){
			return;
		}
		
		$category_sql = " and c.id=".$article_info["category_id"];
		$topClicks = getCRecent($category_sql,"1","a.click_times");
		$smarty->assign("click_artile",$topClicks);
		
		$links = getLinks();
		$advert = getadvert();
		$smarty->assign("links",$links);
		$smarty->assign("advert",$advert);
		$smarty->assign("id",$id);
		$smarty->assign("artileid",$artileid);
		$smarty->assign("article",$article_info["title"]);
		$smarty->assign("activeIdx",$article_info["category_id"]);
		$smarty->assign("title",$title);
		$smarty->assign("chpctx",$chpctx);
		$novel_body = $smarty->fetch('chpctx_bootstrap_1.htm');
		$content_right = $smarty->fetch("right-nav.htm");
		$novel_footer = $smarty->fetch("common_footer.htm");
		makehtml(WEB_ROOT.$localurl,$novel_body.$content_right.$novel_footer);
	}
	
	function showErrorPage($id){
		global $smarty;
		$smarty->assign("id",$id);
		$smarty->display("chpctx_bootstrap_error.htm");
	}
	
?>