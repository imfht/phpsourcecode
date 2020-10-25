<?php
class N81ZW_Parser{

	
/**
	 * 
	 * Enter description here ...���������httpͷ��˵�����·������Ҫ�޸�Ϊ����·��
	 * @param unknown_type $base_domain
	 * @param unknown_type $info
	 */
	function addBaseifAbsent($base_domain,$info){
		$ret = $info;
		if(!strpos(strtolower($info), 'ttp://')){
			$ret = $base_domain.$info;
		}
		return $ret;
	}
	
	function parse_level1($seed_url){
		$base_domain="http://www.81zw.com";
		$contents = myfile_get_content($seed_url);
		$preg ="#<div class=\"book_news_style\">(.*)<div class=\"clear\"></div>(.*)</div>#iUs";
		preg_match_all($preg,$contents,$arr);
			
		$body = $arr[0][0];
			
		$html = str_get_html($body);
		// Find all article blocks
		foreach($html->find('div.book_news_style_form') as $article) {
			$item['img']     = $this->addBaseifAbsent($base_domain,$article->find('img', 0)->src);
			$info = $article->find('div.book_news_style_text', 0);
			$item['title'] = $info->find('h1 a', 0)->innertext;
			$item['url'] = $this->addBaseifAbsent($base_domain,$info->find('h1 a', 0)->href);
			$tmp = $info->find('h2', 0)->innertext;
			$item['author'] = $this->getAuthor($tmp);
			$articles[] = $item;
		}
			
		//print_r($articles);
		return $articles;
	}
	
	function getAuthor($info){
		//print_r($info);
		//$preg ="#���ߣ�(.*) ��#iUs";
		//preg_match($preg,$info,$arr);
		return substr($info, 6);
		//print_r($arr);
		//return $arr[1];
	}
	
	function parse_level2($article_url){
		//echo "url:".$article_url."<br>";
		
		$article_info = array();
		
		$chplst = array();
		$contents = myfile_get_content($article_url);
		$html = str_get_html($contents);
		//$$(".book_news_style_text2 h2")[3]
		$status = $html->find('.book_news_style_text2 h2',3)->innertext;
		//$$(".msgarea")[0]
		$comment = $html->find('.msgarea',0)->xmltext;
		$comment = mb_convert_encoding( $comment, "gb2312","utf8"); 
		//$$("#chapterlist li a")[0].href
		foreach($html->find('#chapterlist li a') as $chapter) {
			$item['url']     = $this->addBaseifAbsent($article_url,$chapter->href);
			$item['title'] = $chapter->innertext;
			$chplst[] = $item;
		}
		
		if(strpos($status,"��")>0){
			$status = 0;
		}else{
			$status = 1;
		}
		
		$article_info["status"] = $status;
		$article_info["comment"] = convert($comment);
		$article_info["chplst"] = $chplst;
		
		return $article_info;
		//print_r($article_info);
		
	}
	
	function parse_level3($chapter_url){
		if (!function_exists('writeLog')) {
			include_once  WEB_ROOT."util/util.php";
		}
		$contents = myfile_get_content($chapter_url);
		//writeLog("parse_level3 entry!contents,".$contents);
		$preg="#<!--go-->(.*)<!--over-->#iUs";
		preg_match_all($preg,$contents,$arr);
		if(strlen($contents)<1){
			return;
		}else{
			$chpctx = $arr[1][0];
		}

		if(strlen($chpctx)<1){
			$preg="#<div id=\"content\">(.*)</div>#iUs";
			preg_match_all($preg,$contents,$arr);
			$chpctx = $arr[1][0];
		}
		//writeLog($chpctx);
		return $chpctx;
	}
}
?>