<?php
	include_once "init.php";
	include_once WEB_ROOT."util/mysql_class.php";
	include_once WEB_ROOT."smarty_inc.php";
	include_once WEB_ROOT."admin/funcs/make_article_func.php";
	include_once WEB_ROOT."admin/funcs/app_util_func.php";
	include_once WEB_ROOT."admin/funcs/make_single_chapter_func.php";
	include_once WEB_ROOT."util/util.php";
	include_once WEB_ROOT."util/Model_class.php";
	include_once WEB_ROOT.'admin/Parser_class.php';
	include_once 'third_part/simple_html_dom.php';
	include_once 'util/mobile_redirect.php';
	
	//$db =  new mysql();
	if(@$_GET["chapter_id"]){
		$chapter_id = str_check($_GET["chapter_id"]);
		$article_id = str_check($_GET["article_id"]);
		$flag = $_GET["flag"];

		if(strcmp($flag,"next")==0){
			//echo "1";
			update_clicktimes($article_id);
			$next_id = next_chapter_func($chapter_id,$article_id);
		}else{
			//echo "2";
			$next_id = last_chapter_func($chapter_id,$article_id);
			update_clicktimes($article_id);
		}
		//echo $next_id;
		navToChapter($next_id);
	}else{
		echo "artileid missing";
	}

	function update_clicktimes($article_id){
		try{
			$db =  new mysql();
			$sql_select="update t_article set click_times = click_times+1 where id=".$article_id;
			$query = $db->query($sql_select);
		}catch (Exception $e){
			
		}
	}
	
	function next_chapter_func($chapter_id,$article_id){
		$db =  new mysql();
		$sql_select="select * from t_chapter where  id > ".$chapter_id." and artile_id=".$article_id." order by id  limit 0,1";
		$query = $db->query($sql_select);
		$count = 0;
		
		$row=$db->fetch_row_array($query);
		if($row){
			return $row["id"];
		}else{
			return -1;
		}
	}
	
	function last_chapter_func($chapter_id,$article_id){
		$db =  new mysql();
		$sql_select="select * from t_chapter where  id < ".$chapter_id." and artile_id=".$article_id." order by id desc  limit 0,1";
		$query = $db->query($sql_select);
		$count = 0;
		
		$row=$db->fetch_row_array($query);
		if($row){
			return $row["id"];
		}else{
			return -1;
		}
	}
	
	function navToChapter($chapter_id){
		if($chapter_id<0){
			echo "<head><meta charset=\"gb2312\"></head>已到最后页	";
			return;
		}
		$local_url="/data/chapters/".$chapter_id.".htm";
		setcookie('history',$local_url,time()+3600*24*7,"/");
		$act_file = WEB_ROOT.$local_url;
		if(!file_exists($act_file)){
			try{
				make_chapter_func($chapter_id,false);
			}catch(Exception $e){
				
			}
		}
		
		if(file_exists($act_file)){
			echo read_file_content($act_file);
		}else{
			showErrorPage($chapter_id);
		}
	}
?>