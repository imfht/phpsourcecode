<?php
/**@name 线下沙龙*/

//分类备注
$categories['07']['COMMENT'] = explode("\r\n", $categories['07']['COMMENT']);

$menu['salon'] = 'nav-active';
$page_title = "线下沙龙 - {$page_title}";

if(empty($_var['op'])){
	$count = $_article->get_count('07');
	if($count){
		$perpage = 10;
		
		$start = ($_var['page'] - 1) * $perpage;
		$pages = @ceil($count / $perpage);
		
		$articles = $_article->get_list('07', $start, $perpage);
		
		$timer = time();
		foreach ($articles as $key => $article){
			$article['BOOK'] = 0;
			
			$article['MODULE'] = explode('|', $article['MODULE']);
			if($article['MODULE'][0] == 'book'){
				if($article['MODULE'][1] + 0 > 0 && $timer < strtotime($article['MODULE'][1])) $article['BOOK'] = 1;
				elseif($article['MODULE'][2] + 0 > 0 && $timer > strtotime($article['MODULE'][2])) $article['BOOK'] = 2;
			}else $article['BOOK'] = 3;
			
			$articles[$key] = $article;
		}
		
		$pager = pager($count, $perpage, $_var['page'], "salon.html", $perpage, false);
	}
	
	include_once view('/tpl/_cms/view/pc_salon');
	exit(0);
}

if($_var['op'] == 'detail'){
	$_book = new book\model\_book();
	
	$_var['gp_id'] = $_var['gp_id'] + 0 ;
	if($_var['gp_id'] == 0) $this->show_message('很抱歉！发生错误了。', 'index.html');
	
	$article = $_article->get_by_id($_var['gp_id']);
	if(!$article) $this->show_message('很抱歉！发生错误了。', 'index.html');
	
	$timer = time();
	
	$article['BOOK'] = 0;
	
	$article['MODULE'] = explode('|', $article['MODULE']);
	if($article['MODULE'][0] == 'book'){
		if($article['MODULE'][1] + 0 > 0 && $timer < strtotime($article['MODULE'][1])) $article['BOOK'] = 1;
		elseif($article['MODULE'][2] + 0 > 0 && $timer > strtotime($article['MODULE'][2])) $article['BOOK'] = 2;
	}else $article['BOOK'] = 3;
	
	if($_var['gp_do'] == 'book'){
		if($_var['gp_formsubmit']){
			$_var['gp_json'] = stripslashes($_var['gp_json']);
			$json = json_decode($_var['gp_json'], 1);
			
			if(empty($json['txtRealName'])) exit_json_message('请输入姓名！');
			if(empty($json['txtMobile'])) exit_json_message('请输入手机号码！');
			if(strtolower($json['txtSeccode']) != $_SESSION['_seccode']) exit_json_message('验证码不正确！');
			
			$book_count = $_book->get_count("AND a.ABOUTID = '{$article[ARTICLEID]}' AND a.ABOUTTYPE = 'salon' AND a.CONNECT = '{$json[txtMobile]}'");
			if($book_count) exit_json_message('此手机号码已报名！');
			else{
				$_book->insert(array(
				'TITLE' => $article['TITLE'], 
				'REMARK' => '', 
				'ABOUTID' => $article['ARTICLEID'], 
				'ABOUTTYPE' => 'salon', 
				'REALNAME' => utf8substr($json['txtRealName'], 0, 30), 
				'CONNECT' => utf8substr($json['txtMobile'], 0, 30), 
				'BOOKDATE' => date('Y-m-d H:i:s'), 
				'USERID' => $_var['current']['USERID'], 
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'STATUS' => 0, 
				'AUTH' => $_var['auth'], 
				'ADDRESS' => $_var['clientip'], 
				'INVITE' => ''
				));
				
				exit_json_message('恭喜您报名成功，请及时关注最新消息！', true);
			}
		}
		
		include_once view('/tpl/_cms/view/pc_salon_book_form');
		exit(0);
	}
	
	$_article->flash_hits($article['ARTICLEID']);
	
	$page_title = "{$article[TITLE]} - {$page_title}";
	
	include_once view('/tpl/_cms/view/pc_salon_detail');
}
?>