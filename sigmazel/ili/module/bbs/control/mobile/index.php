<?php
//版权所有(C) 2014 www.ilinei.com
if(!defined('INIT')) exit('Access Denied');

require_once ROOTPATH.'/module/bbs/actions/mobile_lang.php';

class index{
    public function index(){
        if(!$_var['current']){
            header("location:mobile.do?ac=member&op=login&ref=bbs|{$_var[gp_op]}|{$_var[gp_do]}|{$_var[gp_id]}");
            exit(0);
        }
        
        $file_ranges = range(1, 4);
        $faces_list = bbs_get_faces();
        
        $nav_title = $GLOBALS['lang']['bbs'];
        
        
    	$forum = $_var['gp_id'] ? forum_get_by_id($_var['gp_id']) : forum_get_first();
    	$forum = forum_get_stat($forum);
    	
    	$perpage = 5;
    	
    	$count = topic_get_count($forum['FORUMID']);
    	if($count){
    		$pages = @ceil($count / $perpage);
    		$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
    		$start = ($_var['page'] - 1) * $perpage;
    		
    		$userids = array();
    		$topics = array();
    		$topics = topic_get_list($forum['FORUMID'], $start , $perpage);
    		
    		foreach($topics as $key => $topic){
    			$topics[$key]['SUMMARY'] = nl2br($topic['SUMMARY']);
    			$topics[$key]['SUMMARY_CUT'] = cutstr(strip2words($topic['SUMMARY']), 120);
    			$topics[$key]['SUMMARY_MORE'] = substr($topics[$key]['SUMMARY_CUT'], -3) == '...';
    			$topics[$key]['UPUSERS'] = unserialize($topic['UPUSERS']);
    			
    			if(!$topics[$key]['SUMMARY_MORE']) $topics[$key]['SUMMARY_CUT'] = $topics[$key]['SUMMARY'];
    			
    			$userids[] = $topic['USERID'];
    			$topics[$key]['POST'] = post_get_list($topic['FORUM_TOPICID'], 0, 10, 'AND a.FIRST = 0', 'ORDER BY a.EDITTIME DESC');
    		}
    		
    		$users = $_user->get_list(0, 5, "AND a.USERID in(".eimplode($userids).")");
    		foreach ($users as $key => $user){
    			$user['USERNAME'] = $user['WX_FANSID'] ? $user['REALNAME'] : $user['USERNAME'];
    			!$user['USERNAME'] && $user['USERNAME'] = $user['MOBILE'] ? format_mobile_privacy($user['MOBILE']) : '网友';
    			
    			foreach($topics as $tkey => $topic){
    				if($topic['USERID'] == $user['USERID']) $topics[$tkey]['USER'] = $user;
    			}
    		}
    		
    		$pager = pager($count, $perpage, $_var['page'], "mobile.do?ac=bbs&id={$forum[FORUMID]}", $perpage, false);
    	}
    	
    	if($_var['gp_do'] == 'more') include_once view('/module/bbs/theme/mobile_index_more');
    	else include_once view('/module/bbs/theme/mobile_index');
    }
    
    public function post(){
    	if($_var['gp_formsubmit']){
    		$nowtimer = time();
    		if($_SESSION['_lasttimer'] + 0 > $nowtimer - 30) mobile_show_message($GLOBALS['lang']['bbs.topic.error.limit']);
    		if(empty($_var['gp_txtContent'])) mobile_show_message($GLOBALS['lang']['bbs.topic.error.content']);
    		
    		$_var['gp_txtTitle'] = cutstr(strip2words($_var['gp_txtContent']), 35);
    		$_var['gp_txtContent'] = utf8substr(strip2words($_var['gp_txtContent']), 0, 500);
    		
    		$files = array();
    		foreach($_var['gp_hdnImagePath'] as $key => $val){
    			$temparr = explode('|', $val);
    			if(count($temparr) != 3) continue;
    			
    			$temparr[0] = str_replace('attachment/', '', $temparr[0]);
    			$fileext = get_file_ext($temparr[0]);
    			
    			if($temparr[2] == 1 && substr($temparr[0], -(strlen($fileext) + 3)) == '.t.'.$fileext){
    				$temparr[0] = substr($temparr[0], 0, -(strlen($fileext) + 3));
    			}
    			
    			$files[] = implode('|', $temparr);
    			unset($fileext);
    			unset($temparr);
    		}
    		
    		$topicid = topic_insert(array(
    		'TITLE' => $_var['current']['USERNAME'].':'.$_var['gp_txtTitle'],
    		'ADDRESS' => $_var['clientip'],
    		'SUMMARY' => $_var['gp_txtContent'], 
    		'FORUMID' => $_var['gp_id'], 
    		'ISAUDIT' => 1,
    		'ISTOP' => $_var['gp_cbxIsTop'] + 0,
    		'ISPOST' => $_var['gp_cbxIsPost'] + 0,
    		'CLOSED' => $_var['gp_cbxClosed'] + 0,
    		'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
    		'KEYWORDS' => $_var['gp_txtKeywords'],
    		'USERID' => $_var['current']['USERID'],
    		'USERNAME' => $_var['current']['USERNAME'],
    		'EDITTIME' => date('Y-m-d H:i:s'),
    		'FILE01' => $files[0],
    		'FILE02' => $files[1],
    		'FILE03' => $files[2],
    		'FILE04' => $files[3]
    		));
    		
    		if($topicid){
    			post_insert(array(
    			'TITLE' => $_var['gp_txtTitle'],
    			'CONTENT' => $_var['gp_txtContent'],
    			'FORUMID' => $forum['FORUMID'],
    			'FORUM_TOPICID' => $topicid,
    			'FIRST' => 1,
    			'ADDRESS' => $_var['clientip'],
    			'USERID' => $_var['current']['USERID'],
    			'USERNAME' => $_var['current']['USERNAME'],
    			'EDITTIME' => date('Y-m-d H:i:s')
    			));
    		}
    		
    		header("location:mobile.do?ac=bbs&id={$_var[gp_id]}");
    		exit(0);
    	}
    	
    	$page_title = $GLOBALS['lang']['bbs.title.post'];
    	$_var['referer'] .= '?ac=bbs';
    	
    	include_once view('/module/bbs/theme/mobile_edit');
    }
    
    public function _update(){
    	$topic = topic_get_by_id($_var['gp_topicid'] + 0);
    	if(!$topic) mobile_show_message('参数错误!');
    	
    	$first_post = post_get_first($topic['FORUM_TOPICID']);
    	$topic['CONTENT'] = $first_post['CONTENT'];
    	
    	$topic = format_row_files($topic);
    	$topic_files = topic_get_files($topic, 4);
    	
    	if($_var['gp_formsubmit']){
    		if(empty($_var['gp_txtContent'])) mobile_show_message($GLOBALS['lang']['bbs.topic.error.content']);
    		
    		$_var['gp_txtTitle'] = cutstr(strip2words($_var['gp_txtContent']), 35);
    		$_var['gp_txtContent'] = utf8substr(strip2words($_var['gp_txtContent']), 0, 500);
    		
    		$files = array();
    		foreach($_var['gp_hdnImagePath'] as $key => $val){
    			$temparr = explode('|', $val);
    			if(count($temparr) != 3) continue;
    			
    			$temparr[0] = str_replace('attachment/', '', $temparr[0]);
    			$fileext = get_file_ext($temparr[0]);
    			
    			if($temparr[2] == 1 && substr($temparr[0], -(strlen($fileext) + 3)) == '.t.'.$fileext){
    				$temparr[0] = substr($temparr[0], 0, -(strlen($fileext) + 3));
    			}
    			
    			$files[] = implode('|', $temparr);
    			unset($fileext);
    			unset($temparr);
    		}
    		
    		topic_update($topic['FORUM_TOPICID'], array(
    		'TITLE' => $_var['current']['USERNAME'].':'.$_var['gp_txtTitle'],
    		'ADDRESS' => $_var['clientip'],
    		'SUMMARY' => $_var['gp_txtContent'], 
    		'FILE01' => $files[0],
    		'FILE02' => $files[1],
    		'FILE03' => $files[2],
    		'FILE04' => $files[3]
    		));
    		
    		post_update($first_post['FORUM_POSTID'], array(
    		'TITLE' => $_var['gp_txtTitle'],
    		'CONTENT' => $_var['gp_txtContent']
    		));
    		
    		header("location:mobile.do?ac=bbs&id={$_var[gp_id]}");
    		
    		exit(0);
    	}
    	
    	$page_title = $GLOBALS['lang']['bbs.title.edit'];
    	$_var['referer'] .= '?ac=bbs';
    	
    	include_once view('/module/bbs/theme/mobile_edit');
    }
    
    public function delete(){
    	$topicid = $_var['gp_topicid'] + 0;
    	if($topicid == 0) exit_json_message('帖子参数错误！');
    	
    	$topic = topic_get_by_id($topicid);
    	if(!$topic) exit_json_message('帖子参数错误！');
    	
    	topic_delete($topicid);
    	
    	exit_json(array('success' => true));
    }
    
    public function reply(){
    	$topicid = $_var['gp_topicid'] + 0;
    	if($topicid == 0) exit_json_message('帖子参数错误1！');
    	
    	$topic = topic_get_by_id($topicid);
    	if($topicid == 0) exit_json_message('帖子参数错误2！');
    	
    	if(!$_var['current']) exit_json_message('未登录，不能回复！');
    	if(empty($_var['gp_content'])) exit_json_message('评论内容不能为空！');
    	
    	$comment = '';
    	if($_var['gp_postid'] + 0 > 0){
    		$post = post_get_by_id($_var['gp_postid'] + 0);
    		$post && $comment = $post['TITLE'];
    	}
    	
    	$content = strip2words($_var['gp_content']);
    	
    	post_insert(array(
    	'TITLE' => $_var['current']['USERNAME'], 
    	'CONTENT' => $content, 
    	'COMMENT' => $comment, 
    	'FORUMID' => $topic['FORUMID'],
    	'FORUM_TOPICID' => $topic['FORUM_TOPICID'],
    	'FIRST' => 0,
    	'ADDRESS' => $_var['clientip'],
    	'USERID' => $_var['current']['USERID'],
    	'USERNAME' => $_var['current']['USERNAME'],
    	'EDITTIME' => date('Y-m-d H:i:s')
    	));
    	
    	$content = bbs_face_format($content);
    	
    	exit_json(array('success' => true, 'content' => $content));
    }
    
    public function reply_more(){
    	if($_var['gp_topicid'] + 0 == 0) exit_echo('');
    	
    	$count = post_get_count($_var['gp_topicid'], 'AND a.FIRST = 0');
    	if($count){
    		$perpage = 10;
    		$pages = @ceil($count / $perpage);
    		$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
    		$start = ($_var['page'] - 1) * $perpage;
    		
    		$posts = post_get_list($_var['gp_topicid'], $start, $perpage, 'AND a.FIRST = 0', 'ORDER BY a.EDITTIME DESC');
    		
    		include_once view('/module/bbs/theme/mobile_reply_more');
    	}
    }
    
    public function up(){
    	$topicid = $_var['gp_topicid'] + 0;
    	if($topicid == 0) exit_json_message('帖子参数错误！');
    	
    	$topic = topic_get_by_id($topicid);
    	if(!$topic) exit_json_message('帖子参数错误！');
    	
    	$topic['UPUSERS'] = unserialize($topic['UPUSERS']);
    	if($topic['UPUSERS'][$_var['current']['USERID']]) exit_json(array('success' => false));
    	
    	$topic['UPUSERS'][$_var['current']['USERID']] = $_var['current']['USERNAME'];
    	
    	topic_update($topicid, array('UP' => $topic['UP'] + 1, 'UPUSERS' => serialize($topic['UPUSERS'])));
    	
    	exit_json(array('success' => true, 'username' => $_var['current']['USERNAME']));
    }
    
    public function upload(){
    	$json_message = array('success' => false, 'message' => '');
    	
    	if($_FILES['flePath']['name']){
    		$upload = new \ilinei\upload();
    		$cimage = new \ilinei\image();
    		
    		$upload->init($_FILES['flePath'], 'portal');
    		$upload->save();
    		
    		if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
    		if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
    		if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
    		
    		if($upload->attach){
    			$thumb = thumb_image($cimage, $upload->attach['target'], array('ImageWidth' => 300, 'ImageHeight' => 200));
    			$json_message['success'] = true;
    			$json_message['path'] = format_file_path($upload->attach['target'], $thumb);
    			$json_message['name'] = $upload->attach['name'];
    			$json_message['thumb'] = $thumb;
    		}else $json_message['message'] = $GLOBALS['lang']['admin.validate.swfupload.echo.fail'];
    	}
    	
    	exit_json($json_message);
    }

}
?>