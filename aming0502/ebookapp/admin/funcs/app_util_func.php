<?php

	
	function get_parse_class($seed_id){
		$model = new Model();
		$condition ="id=".$seed_id;
		$seeds = $model->select("t_seeds","*",$condition);
		$seed = $seeds[0];
		//print_r($seed);
		return $seed["parse_class"];
	}
	
	function getadvert(){
		$model =  new Model();
		return $model->select("t_adsence","text","aviable=1");
	}
	
	function getLinks(){
		$model =  new Model();
		return $model->select("t_links");
	}
	
	function getRecent(){
		$model = new Model();
		
		if(@$_GET["id"]){
			$id = verify_id($_GET["id"]);
			$category_id = " and c.id=".$id;
		}else{
			$category_id ="";
		}
		$sql_select="select a.*,c.category_name from t_article a,t_seeds b,t_category c where a.seed_id = b.id and b.category_id=c.id ".$category_id." order by a.id desc limit 0,30";
		
		$model->query($sql_select);
		
		return $model->fetch_all();
	}
	
	function getCRecent($category_sql,$pageNo,$order_by){
		$model = new Model();
		
		$startIdx = ($pageNo-1)*15;
		//$endIdx = $pageNo*15;
		$sql_select="select a.*,c.category_name from t_article a,t_seeds b,t_category c where a.seed_id = b.id and b.category_id=c.id ".$category_sql." order by $order_by desc limit ".$startIdx.",15";
		//echo $sql_select;
		$model->query($sql_select);
		
		return $model->fetch_all();
	}
	
	
	function getPageInfo($category_id,$pageNo,$maxPg){
		$startPage = $pageNo-5<=0?1:$pageNo-5;
		$endPage = $maxPg-$startPage<10?$maxPg:10+$startPage;
		
		//$pagination = "<li><a href=\"/category/".$category_id."/".($pageNo+1).".html\">Next</a></li>";
		$pagination="";
		for($page=$startPage;$page<=$endPage;$page++){
			$pagination = $pagination."<li><a href=\"/category/".$category_id."/".($page).".html\">$page</a></li>";
		}
		
		return $pagination;
		
	}
	
	
	
	function get_article_info($artileid){
		$db =  new mysql();
		//$sql_select="select * from t_article where id = ".$artileid;
		$sql_select="select a.*,b.category_id,b.parse_class from t_article a,t_seeds b where a.id = ".$artileid." and a.seed_id = b.id";
		$query = $db->query($sql_select);
		$article_info = $db->fetch_row_array($query);
		return $article_info;
	}
	
	function get_category_name($category_id){
		$db =  new mysql();
		$sql_select="select * from t_category where id = ".$category_id;
		$query = $db->query($sql_select);
		$titlerow = $db->fetch_row_array($query);
		$category_name = $titlerow["category_name"];
		return $category_name;
	}
	
	function get_total_count($sql_select){
		$db =  new mysql();
		$query = $db->query($sql_select);
		$titlerow = $db->fetch_row_array($query);
		$total_count = $titlerow[0];
		return $total_count;
	}
	
	function read_file_content($FileName)
	{
		//open file
		$fp=fopen($FileName,"r");
		$data="";
		while(!feof($fp))
		{
			$data.=fread($fp,4096);
		}
		//close the file
		fclose($fp);
		return $data;
	} 
	
	
	
?>