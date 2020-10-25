<?php
/**@name 关键词*/

if(empty($_var['op'])){
	$articles = $_article->get_list('', 0, 50, "AND c.IDENTITY LIKE 'B3%'");
	
	include_once view("/tpl/_cms/view/pc_api");
}elseif($_var['op'] == 'text'){
	$id = $_var['gp_id'] + 0;
	if($id <= 0)  mobile_show_message($GLOBALS['lang']['error'], $setting['MobileIndex'] ? $setting['MobileIndex'] : 'index.php');
	
	$article = $_article->get_by_id($id);
	if(!$article)  mobile_show_message($GLOBALS['lang']['error'], $setting['MobileIndex'] ? $setting['MobileIndex'] : 'index.php');
	
	$article['CONTENT'] = preg_replace('/<img[^>]+src\s*=\s*"?([^>"\s]+)"?[^>]*>/i', '<img src="$1"/>', strip_tags($article['CONTENT'], '<p><span><br><img><a><embed>'));
	
	if($article['IDENTITY'] == 'B33' && strexists($article['CONTENT'], "http://")){
		header("location:{$article[CONTENT]}");
		exit(0);
	}
	
	$article = format_row_mp4($article, 'CONTENT');
	$article = format_row_mp3($article, 'CONTENT');
	
	$_article->flash_hits($article['ARTICLEID']);
	
	$page_title = $article['TITLE'];
	
	include_once view("/tpl/_cms/view/pc_api_text");
}
?>