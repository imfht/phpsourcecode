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
	
	if(@$_GET["id"]){
		$chapter_id = str_check($_GET["id"]);
		try{
			navToChapter($chapter_id);
		}catch (Exception $e){
			echo $e->getMessage(), '<br/>';
			echo '<pre>', $e->getTraceAsString(), '</pre>';
		}
	}else{
		echo "artileid missing";
	}
	
	function navToChapter($chapter_id){
		$local_url="/data/chapters/".$chapter_id.".htm";
		setcookie('history',$local_url,time()+3600*24*7,"/");
		$act_file = WEB_ROOT.$local_url;
		if(!file_exists($act_file)){
			try{
				make_chapter_func($chapter_id,false);
			}catch(Exception $e){
				echo $e->getMessage(), '<br/>';
				echo '<pre>', $e->getTraceAsString(), '</pre>';
			}
		}
		
		if(file_exists($act_file)){
			echo read_file_content($act_file);
		}else{
			showErrorPage($chapter_id);
		}
	}
?>