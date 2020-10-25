<?php
/**@name 社区*/

//当前导航
$menu['forum'] = 'nav-active';
$page_title = "社区 - {$page_title}";

$_user = new user\model\_user();
$_forum = new bbs\model\_forum();
$_forum_topic = new bbs\model\_forum_topic();
$_forum_post = new bbs\model\_forum_post();

//讨论区限8个
$forums = $_forum->get_list(0, 8);
foreach ($forums as $key => $forum){
	$forums[$key]['REMARK'] = nl2br($forum['REMARK']);
}

if(empty($_var['op'])){
	//概览
	$article_051 = $_article->get_first('051', '', '', 1);
	$article_051_faces = array();
	for($i = 1; $i <= 6; $i++){
		if(is_array($article_051['FILE0'.$i])) $article_051_faces[] = $article_051['FILE0'.$i];
	}
	
	//加入团队，每类限10个
	$category_jobs = $_category->get_children_by_identity('053', 'article');
	
	foreach ($category_jobs as $key => $jobs){
		$category_jobs[$key]['ARTICLES'] = $_article->get_list($jobs['IDENTITY'], 0, 10);
	}
	
	include_once view('/tpl/_cms/view/pc_forum');
	exit(0);
}

if(in_array($_var['op'], array('topics', 'pub'))){
	$_var['gp_id'] = $_var['gp_id'] + 0 ;
	if($_var['gp_id'] == 0) $this->show_message('很抱歉！发生错误了。', 'index.html');
	
	$forum = $_forum->get_by_id($_var['gp_id']);
	if(!$forum) $this->show_message('很抱歉！发生错误了。', 'index.html');
	
	//查询及排序
	$wheresql = $ordersql = '';
	if($_var['gp_tag'] == 'hit') $ordersql = 'ORDER BY a.HITS DESC';
	elseif($_var['gp_tag'] == 'top') $wheresql = 'AND a.ISTOP = 1';
	else $_var['gp_tag'] = 'all';
	
	//限用户角色
	$forum['ERR'] = 0;
	if($forum['GROUP']){
		$forum['GROUPS'] = $_forum->get_groups($forum['GROUP']);
		if(!$forum['GROUPS'][$_var['current']['GROUPID']]){
			$forum['ERR'] = 1;
			$wheresql = 'AND 1=0';
		}
	}
	
	//主题总数
	$topic_count = $_forum_topic->get_count();
	
	//最近一期沙龙，手机不支持
	if(!$ismobile) $salon = $_article->get_first('07');
}

if($_var['op'] == 'topics'){
	//格式化版规
	$forum['RULE'] = nl2br($forum['RULE']);
	
	//回复数量
	$forum['POSTCOUNT'] = $_forum_post->get_count("AND a.FORUMID = '{$forum[FORUMID]}' AND a.FIRST = 0");
	
	$count = $_forum_topic->get_count("AND a.FORUMID = '{$forum[FORUMID]}' {$wheresql}");
	if($count){
		$perpage = 10;
		
		$start = ($_var['page'] - 1) * $perpage;
		$pages = @ceil($count / $perpage);
		
		$topics = $_forum_topic->get_list($start, $perpage, "AND a.FORUMID = '{$forum[FORUMID]}' {$wheresql}", $ordersql);
		
		$pager = pager($count, $perpage, $_var['page'], "forum.html?op=topics&tag={$_var[gp_tag]}&id={$_var[gp_id]}", $perpage, false);
	}
	
	$page_title = "{$forum[NAME]} - {$page_title}";
	
	include_once view('/tpl/_cms/view/pc_forum_topics');
}elseif($_var['op'] == 'posts'){
	$_var['gp_id'] = $_var['gp_id'] + 0 ;
	if($_var['gp_id'] == 0) $this->show_message('很抱歉！发生错误了。', 'index.html');
	
	$topic = $_forum_topic->get_by_id($_var['gp_id']);
	if(!$topic) $this->show_message('很抱歉！发生错误了。', 'index.html');
	
	$count = $_forum_post->get_count("AND a.FORUM_TOPICID = '{$topic[FORUM_TOPICID]}' AND a.FIRST = 0");
	if($count){
		$perpage = 10;
		
		$start = ($_var['page'] - 1) * $perpage;
		$pages = @ceil($count / $perpage);
		
		$posts = $_forum_post->get_list($start, $perpage, "AND a.FORUM_TOPICID = '{$topic[FORUM_TOPICID]}' AND a.FIRST = 0", 'ORDER BY a.EDITTIME DESC');
		foreach ($posts as $key => $post){
			if($post['COMMENT']) $posts[$key]['TITLE'] = explode('#', $post['TITLE']);
		}
	}
	
	if($_var['gp_cmd'] == 'more'){
		include_once view('/tpl/_cms/view/pc_forum_posts_more');
		exit(0);
	}
	
	//格式化主题
	$topic = $_forum_topic->format($topic); 
	$topic['UPUSERS'] = $topic['UPUSERS'] ? explode('#', $topic['UPUSERS']) : array();
	
	$_forum_topic->flash_hits($topic['FORUM_TOPICID']);
	
	$forum = $_forum->get_by_id($topic['FORUMID']);
	
	//限用户角色
	$forum['ERR'] = 0;
	if($forum['GROUP']){
		$forum['GROUPS'] = $_forum->get_groups($forum['GROUP']);
		if(!$forum['GROUPS'][$_var['current']['GROUPID']]){
			$forum['ERR'] = 1;
		}
	}
	
	if($forum['ERR'] == 1) $this->show_message('您无此权限！', 'forum.html');
	
	$forum['ERR'] = -1;
	$user = $_user->get_by_id($topic['USERID']);
	
	$user['TOPICCOUNT'] = $_forum_topic->get_count("AND a.USERID = '{$user[USERID]}'");
	$user['POSTCOUNT'] = $_forum_post->get_count("AND a.USERID = '{$user[USERID]}' AND a.FIRST = 0");
	$user['MODULECOUNT'] = 0;
	
	//主题总数
	$topic_count = $_forum_topic->get_count();
	
	//最近一期沙龙，手机不支持
	if(!$ismobile) $salon = $_article->get_first('07');
	
	$page_title = "{$topic[TITLE]} - {$page_title}";
	
	include_once view('/tpl/_cms/view/pc_forum_posts');
}elseif($_var['op'] == 'pub'){
	if(!$_var['current']) $this->show_message('未登录不能发布主题！', 'forum.html');
	if($forum['ERR'] == 1) $this->show_message('您无此权限！', 'forum.html');
	
	$user = $_var['current'];
	
	$user['TOPICCOUNT'] = $_forum_topic->get_count("AND a.USERID = '{$user[USERID]}'");
	$user['POSTCOUNT'] = $_forum_post->get_count("AND a.USERID = '{$user[USERID]}'");
	$user['MODULECOUNT'] = 0;
	
	//表单提交
	if($_var['gp_formsubmit']){
		$nowtimer = time();
		if($_SESSION['_lasttimer'] + 0 > $nowtimer - 30) $this->show_message('30秒内只能提交一次！');
		
		if(empty($_var['gp_txtTitle'])) $this->show_message('主题标题不能为空！');
		if(empty($_var['gp_txtContent'])) $this->show_message('主题内容不能为空！');
		
		$_var['gp_txtTitle'] = utf8substr(strip2words($_var['gp_txtTitle']), 0, 50);
		$_var['gp_txtContent'] = strip2words($_var['gp_txtContent'], false);
		
		$files = array();
		foreach($_var['gp_hdnImagePath_file'] as $key => $val){
			$temparr = explode('|', $val);
			if(count($temparr) < 3) continue;
			
			$temparr[0] = str_replace('attachment/', '', $temparr[0]);
			$fileext = get_file_ext($temparr[0]);
			
			if($temparr[2] == 1 && substr($temparr[0], -(strlen($fileext) + 3)) == '.t.'.$fileext){
				$temparr[0] = substr($temparr[0], 0, -(strlen($fileext) + 3));
			}
			
			$files[] = implode('|', $temparr);
			unset($fileext);
			unset($temparr);
		}
		
		$_var['gp_txtContent'] = nl2br($_var['gp_txtContent']);
		
		$topicid = $_forum_topic->insert(array(
		'TITLE' => $_var['gp_txtTitle'],
		'ADDRESS' => $_var['clientip'],
		'SUMMARY' => $_var['gp_txtContent'], 
		'FORUMID' => $_var['gp_id'], 
		'ISAUDIT' => 1,
		'USERID' => $_var['current']['USERID'],
		'USERNAME' => $_var['current']['REALNAME'],
		'EDITTIME' => date('Y-m-d H:i:s'),
		'FILE01' => $files[0],
		'FILE02' => $files[1],
		'FILE03' => $files[2],
		'FILE04' => $files[3]
		));
		
		if($topicid){
			$_forum_post->insert(array(
			'TITLE' => $_var['gp_txtTitle'],
			'CONTENT' => $_var['gp_txtContent'], 
			'FORUMID' => $forum['FORUMID'], 
			'FORUM_TOPICID' => $topicid,
			'FIRST' => 1,
			'ADDRESS' => $_var['clientip'],
			'USERID' => $_var['current']['USERID'],
			'USERNAME' => $_var['current']['REALNAME'],
			'EDITTIME' => date('Y-m-d H:i:s')
			));
		}

        $this->show_message('恭喜你发布主题成功！', "forum.html?op=topics&id={$_var[gp_id]}");
	}
	
	$forum['ERR'] = -1;
	$_SESSION['_lasttimer'] = time();
	$page_title = "发布主题 - {$page_title}";
	
	include_once view('/tpl/_cms/view/pc_forum_pub');
}elseif($_var['op'] == 'up' || $_var['op'] == 'post'){
	$_var['gp_id'] = $_var['gp_id'] + 0 ;
	if($_var['gp_id'] == 0) exit_json_message('很抱歉！发生错误了。');
	
	$topic = $_forum_topic->get_by_id($_var['gp_id']);
	if(!$topic) exit_json_message('很抱歉！发生错误了。');
	
	if(!$_var['current']) exit_json_message('未登录不能发布主题！');
	
	$forum = $_forum->get_by_id($topic['FORUMID']);
	
	//限用户角色
	$forum['ERR'] = 0;
	if($forum['GROUP']){
		$forum['GROUPS'] = $_forum->get_groups($forum['GROUP']);
		if(!$forum['GROUPS'][$_var['current']['GROUPID']]){
			$forum['ERR'] = 1;
		}
	}
	
	if($forum['ERR'] == 1) exit_json_message('您无此权限！');
	
	//点赞！
	if($_var['op'] == 'up'){
		$topic['UPUSERS'] = $topic['UPUSERS'] ? explode('#', $topic['UPUSERS']) : array();
		if(in_array($_var['current']['USERID'], $topic['UPUSERS'])) exit_json_message('您已经成功点赞！');
		
		$topic['UPUSERS'][] = $_var['current']['USERID'];
		$_forum_topic->update($topic['FORUM_TOPICID'], array('UPUSERS' => implode('#', $topic['UPUSERS'])));
		exit_json_message('', true);
	}
	
	$title = $topic['TITLE'];
	$content = strip2words($_var['gp_content'], false);
	$content = nl2br($content);
	$comment = '';
	
	/**
	 * 回复POST时
	 * title记录POST的ID、用户ID、用户名，以#号间隔
	 * comment记录POST内容摘要
	 */
	
	if($_var['gp_postid'] + 0 > 0){
		$post = $_forum_post->get_by_id($_var['gp_postid'] + 0);
		if($post){
			$comment = str_replace('<br/>', "\r\n", $post['CONTENT']);
			$comment = cutstr(strip2words($comment), 120, '');
			$comment = nl2br($comment);
			$title = "{$post[FORUM_POSTID]}#{$post[USERID]}#{$post[USERNAME]}";
		}
	}
	
	$postid = $_forum_post->insert(array(
	'TITLE' => $title, 
	'CONTENT' => $content, 
	'COMMENT' => $comment, 
	'FORUMID' => $topic['FORUMID'],
	'FORUM_TOPICID' => $topic['FORUM_TOPICID'],
	'FIRST' => 0,
	'ADDRESS' => $_var['clientip'],
	'USERID' => $_var['current']['USERID'],
	'USERNAME' => $_var['current']['REALNAME'],
	'EDITTIME' => date('Y-m-d H:i:s')
	));
	
	exit_json(array(
	'success' => true, 
	'message' => '恭喜你回复成功！', 
	'content' => $content, 
	'comment' => $comment, 
	'postid' => $postid
	));
}elseif($_var['op'] == 'upload'){
	if(!$_var['current'] && $_var['gp__SALT']){
		$tmparr = explode('-', $_var['gp__SALT']);
		$_var['current'] = $_user->get_by_salt($tmparr[0], $tmparr[1]);
	}
	
	if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
	
	$file_limit = $_var['gp_limit'] + 0;
	$file_uploaded = $_var['gp_uploaded'] + 0;
	
	if($file_limit > 0 && $file_limit < $file_uploaded + 1) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.limit']."{$file_limit}".$GLOBALS['lang']['admin.validate.swfupload.echo.limit.pic']);
	
	if($_FILES['Filedata']['name']){
		$upload = new \ilinei\upload();
		$cimage = new \ilinei\image();
		
		$upload->init($_FILES['Filedata'], 'mutual');
		
		if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
		if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
		
		$upload->save();
		if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
		
		if($upload->attach) {
			$thumb = thumb_image($cimage, $upload->attach['target'], array('ImageWidth' => 150, 'ImageHeight' => 150, 'ThumbType' => 1));
			$tempimgsize = getimagesize('attachment/'.$upload->attach['target']);
			
			exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$tempimgsize[0].'|'.$tempimgsize[1].'|'.$_var['gp_id']);
		}
	}
	
	exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
}
?>