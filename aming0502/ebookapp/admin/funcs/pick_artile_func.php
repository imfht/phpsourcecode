<?php

/**
 * 
 * 采集章节列表
 * @param $artileid
 * @param $debug
 */
function pick_article($artileid,$debug){
	$model = new Model();
	$condition ="id=".$artileid;
	$articles = $model->select("t_article","*",$condition);

	//根据parser_class配置获取文章信息
	if($articles){
		$article = $articles[0];
		$seed_id = $article["seed_id"];
		$article_url = $article["url"];
		$parse_class = get_parse_class($seed_id);
		$parserBean = new $parse_class();
		$article_info = $parserBean->parse_level2($article_url);

		$status = $article_info["status"];
		$chplst = $article_info["chplst"];
		//print_r($article_info);
		//写章节列表
		foreach($chplst as $chp){
			//先判断是否采集过
			$rows = $model->select("t_chapter","url","artile_id=".$artileid." and url='".$chp["url"]."'");
			if(!$rows){
				$model->insert("t_chapter", array("artile_id","title","url"), array($artileid,$chp["title"],$chp["url"]));
				if($debug){
					echo "insert url:" .$chp["url"]."]<br>";
				}
			}
		}
			
		//更新文章状态
		$model->update("t_article", array("status"=>$status,"modify_date"=>date('Ymd'),"comment"=>$article_info["comment"]), "id=".$artileid);
		 
		if(!empty($article_info['author'])){
			$author = $article_info['author'] ;
			$model->update("t_article", array("author"=>$author), "id=".$artileid);
		}
			
		if($debug){
			echo "采集完成";
		}
	}
}

/*function pick_artile_content($contents,$baseurl){
 $preg="#<li><a href=\"(.*)\">(.*)</a></li>#iUs";
 preg_match_all($preg,$contents,$arr);
 $idx = 0;
 foreach($arr[1] as $id=>$e_url){
 if(!strpos($e_url,".html",0)){
 continue;
 }
 $acturl = $baseurl.$e_url;
 $cptitle = $arr[2][$id];
 $ret[$idx]["url"]=$acturl;
 $ret[$idx]["title"]=$cptitle;
 $idx++;
 }
 return $ret;
 }*/

/*function get_status($contents){
 $preg="#<h2>连载完成：(.*)...</h2>#iUs";
 preg_match_all($preg,$contents,$arr);
 $status = $arr[1][0];
 if($status=="连载中"){
 $status = 0;
 }else{
 $status = 1;
 }
 return $status;
 }*/
/**
 判断文章是否重新采集
 **/
function is_chapter_updated($articleid){
	 $db =  new mysql();
	 $sql_select="select id from t_article where id=".$articleid." and status=0 and modify_date < '".date('Ymd')."'";
	 $query = $db->query($sql_select);
	 if($db->num_rows($query)>0){
	 return true;
	 }
	 return false;
 }

?>