<?php
//版权所有(C) 2014 www.ilinei.com

namespace bbs\control;

use admin\model\_log;
use admin\model\_table;
use user\model\_user;
use bbs\model\_forum;
use bbs\model\_forum_topic;
use bbs\model\_forum_post;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/bbs/lang.php';

//贴子列表
class topic{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_forum = new _forum();
		$_forum_topic = new _forum_topic();
		$_forum_post = new _forum_post();
		
		$forum_list = $_forum->get_list(0, 0);
		$search = $_forum_topic->search();
		
		if($_var['gp_do'] == 'delete'){
			$topic = $_forum_topic->get_by_id($_var['gp_id']);
			
			if($topic){
				$_forum_topic->delete($topic['FORUM_TOPICID']);
				
				$_log->insert($GLOBALS['lang']['bbs.topic.log.delete']."({$topic[TITLE]})", $GLOBALS['lang']['bbs.topic']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$topic_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$topic = $_forum_topic->get_by_id($val);
				if($topic){
					$_forum_topic->delete($topic['FORUM_TOPICID']);
					
					$topic_titles .= $topic['TITLE'].', ';
				}
				
				unset($topic);
			}
			
			if($topic_titles) $_log->insert($GLOBALS['lang']['bbs.topic.log.delete.list']."({$topic_titles})", $GLOBALS['lang']['bbs.topic']);
		}
		
		if($_var['gp_do'] == 'pass_list' && is_array($_var['gp_cbxItem'])){
			$topic_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$topic = $_forum_topic->get_by_id($val);
				if($topic){
					$_forum_topic->update($topic['FORUM_TOPICID'], array('ISAUDIT' => 1));
					
					$topic_titles .= $topic['TITLE'].', ';
				}
				
				unset($topic);
			}
			
			if($topic_titles) $_log->insert($GLOBALS['lang']['bbs.topic.log.pass.list']."({$topic_titles})", $GLOBALS['lang']['bbs.topic']);
		}
		
		if($_var['gp_do'] == 'fail_list' && is_array($_var['gp_cbxItem'])){
			$topic_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$topic = $_forum_topic->get_by_id($val);
				if($topic){
					$_forum_topic->update($topic['FORUM_TOPICID'], array('ISAUDIT' => 0));
					
					$topic_titles .= $topic['TITLE'].', ';
				}
				
				unset($topic);
			}
			
			if($topic_titles) $_log->insert($GLOBALS['lang']['bbs.topic.log.fail.list']."({$topic_titles})", $GLOBALS['lang']['bbs.topic']);
		}
		
		if($_var['gp_do'] == 'move_list' && is_array($_var['gp_cbxItem'])){
			$move_forum = $_forum->get_by_id($_var['gp_hdnMoveForumID'] + 0);
			if($move_forum){
				$topic_titles = '';
				
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$topic = $_forum_topic->get_by_id($val);
					if($topic){
						$_forum_topic->update($topic['FORUM_TOPICID'], array('FORUMID' => $move_forum['FORUMID']));
						$_forum_post->update_batch("FORUM_TOPICID = '{$topic[FORUM_TOPICID]}'", array('FORUMID' => $move_forum['FORUMID']));
						
						$topic_titles .= $topic['TITLE'].', ';
					}
					
					unset($topic);
				}
				
				if($topic_titles) $_log->insert($GLOBALS['lang']['bbs.topic.log.move.list']."({$topic_titles})", $GLOBALS['lang']['bbs.topic']);
			}
		}
		
		$count = $_forum_topic->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$topics = $_forum_topic->get_list($start, $perpage, $search['wheresql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/bbs/topic{$search[querystring]}", $perpage);
		}
	
		include_once view('/module/bbs/view/topic');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_user = new _user();
		$_forum = new _forum();
		$_forum_topic = new _forum_topic();
		$_forum_post = new _forum_post();
		
		$table = $_table->get_by_identity('forum_topic');
		
		$forum_list = $_forum->get_list(0, 0);
		$search = $_forum_topic->search();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$forumid = $_var['gp_sltForumId'] + 0;
			$forum = $forumid > 0 ? $_forum->get_by_id($forumid) : null;
			
			if(!$forum) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.forum']."<br/>";
			else {
				if(empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.username']."<br/>";
				if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.title']."<br/>";
				if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.content']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$post_user = $_user->get_by_id($_var['gp_txtUserName']);
				
				if($post_user == null) $post_user = $_user->get_by_mobile($_var['gp_txtUserName']);
				if($post_user == null) $post_user = $_user->get_by_email($_var['gp_txtUserName']);
				if($post_user == null) $post_user = $_user->get_by_name($_var['gp_txtUserName']);
				
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtKeywords'] = utf8substr($_var['gp_txtKeywords'], 0, 50);
				
				$topic_file_arr = file_upload_images($table['FILENUM']);
				
				$topicid = $_forum_topic->insert(array_merge(array(
				'TITLE' => $_var['gp_txtTitle'],
				'ADDRESS' => $_var['clientip'],
				'SUMMARY' => utf8substr(strip2words($_var['gp_txtContent']), 0, 200), 
				'FORUMID' => $forum['FORUMID'],
				'ISAUDIT' => $forum['ISAUDIT'] ? 0 : 1, 
				'ISTOP' => $_var['gp_cbxIsTop'] + 0, 
				'ISPOST' => $_var['gp_cbxIsPost'] + 0, 
				'CLOSED' => $_var['gp_cbxClosed'] + 0, 
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'KEYWORDS' => $_var['gp_txtKeywords'],
				'USERID' => $post_user ? $post_user['USERID'] : 0,
				'USERNAME' => $post_user ? ($post_user['WX_FANSID'] ? $post_user['REALNAME'] : $post_user['USERNAME']) : $_var['gp_txtUserName'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				), $topic_file_arr));
				
				$_forum_post->insert(array(
				'TITLE' => $_var['gp_txtTitle'],
				'CONTENT' => $_var['gp_txtContent'], 
				'FORUMID' => $forum['FORUMID'],
				'FORUM_TOPICID' => $topicid, 
				'FIRST' => 1, 
				'ADDRESS' => $_var['clientip'],
				'USERID' => $post_user ? $post_user['USERID'] : 0,
				'USERNAME' => $post_user ? ($post_user['WX_FANSID'] ? $post_user['REALNAME'] : $post_user['USERNAME']) : $_var['gp_txtUserName'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['bbs.topic.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['bbs.topic']);
				
				show_message($GLOBALS['lang']['bbs.topic.message.add'], "{ADMIN_SCRIPT}/bbs/topic");
			}
		}
		
		include_once view('/module/bbs/view/topic_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_user = new _user();
		$_forum = new _forum();
		$_forum_topic = new _forum_topic();
		$_forum_post = new _forum_post();
		
		$table = $_table->get_by_identity('forum_topic');
		
		$forum_list = $_forum->get_list(0, 0);
		$search = $_forum_topic->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/bbs/topic");
		
		$topic = $_forum_topic->get_by_id($id);
		if($topic == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/bbs/topic");
		
		$post_first = $_forum_post->get_first($id);
		$topic['CONTENT'] = $post_first['CONTENT'];
		
		$topic = format_row_files($topic);	
		$topic_files = $_forum_topic->get_files($topic, $table['FILENUM']);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$forumid = $_var['gp_sltForumId'] + 0;
			$forum = $forumid > 0 ? $_forum->get_by_id($forumid) : null;
			
			if(!$forum) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.forum']."<br/>";
			else {
				if(empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.username']."<br/>";
				if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.title']."<br/>";
				if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_edit.validate.content']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$post_user = $_user->get_by_id($_var['gp_txtUserName']);
				
				if($post_user == null) $post_user = $_user->get_by_mobile($_var['gp_txtUserName']);
				if($post_user == null) $post_user = $_user->get_by_email($_var['gp_txtUserName']);
				if($post_user == null) $post_user = $_user->get_by_name($_var['gp_txtUserName']);
				
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtKeywords'] = utf8substr($_var['gp_txtKeywords'], 0, 50);
				
				$topic_file_arr = file_upload_images($table['FILENUM']);
				
				$_forum_topic->update($topic['FORUM_TOPICID'], array_merge(array(
				'TITLE' => $_var['gp_txtTitle'],
				'ADDRESS' => $_var['clientip'],
				'SUMMARY' => utf8substr(strip2words($_var['gp_txtContent']), 0, 200), 
				'FORUMID' => $forum['FORUMID'], 
				'ISTOP' => $_var['gp_cbxIsTop'] + 0, 
				'ISPOST' => $_var['gp_cbxIsPost'] + 0, 
				'CLOSED' => $_var['gp_cbxClosed'] + 0, 
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'KEYWORDS' => $_var['gp_txtKeywords'], 
				'USERID' => $post_user ? $post_user['USERID'] : 0, 
				'USERNAME' => $post_user ? ($post_user['WX_FANSID'] ? $post_user['REALNAME'] : $post_user['USERNAME']) : $_var['gp_txtUserName'], 
				), $topic_file_arr));
				
				$_forum_post->update($post_first['FORUM_POSTID'], array(
				'TITLE' => $_var['gp_txtTitle'],
				'CONTENT' => $_var['gp_txtContent'], 
				'ADDRESS' => $_var['clientip'], 
				'COMMENT' => strip_tags($_var['gp_txtComment']), 
				'USERID' => $post_user ? $post_user['USERID'] : 0, 
				'USERNAME' => $post_user ? ($post_user['WX_FANSID'] ? $post_user['REALNAME'] : $post_user['USERNAME']) : $_var['gp_txtUserName'], 
				));
				
				$_log->insert($GLOBALS['lang']['bbs.topic.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['bbs.topic']);
				
				show_message($GLOBALS['lang']['bbs.topic.message.update'], "{ADMIN_SCRIPT}/bbs/topic&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/bbs/view/topic_edit');
	}
	
	//查看
	public function _view(){
		global $_var;
		
		$_log = new _log();
		$_user = new _user();
		$_forum = new _forum();
		$_forum_topic = new _forum_topic();
		$_forum_post = new _forum_post();
		
		$search = $_forum_topic->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/bbs/topic");
		
		$topic = $_forum_topic->get_by_id($id);
		if($topic == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/bbs/topic");
		
		$forum = $_forum->get_by_id($topic['FORUMID']);
		
		if($_var['gp_do'] == 'delete'){
			$post = $_forum_post->get_by_id($_var['gp_postid']);
			if($post){
				$_forum_post->delete($post['FORUM_POSTID']);
				
				$post_count = $_forum_post->get_count("AND a.FORUM_TOPICID = '{$post[FORUM_TOPICID]}' AND a.FIRST = 0");
				$_forum_topic->update($post['FORUM_TOPICID'], array('POSTCOUNT' => $post_count));
				
				$_log->insert($GLOBALS['lang']['bbs.topic.post.log.delete']."({$post[TITLE]})", $GLOBALS['lang']['bbs.topic']);
			}
		}
		
		$count = $_forum_post->get_count("AND a.FORUM_TOPICID = '{$id}'");
		if($count){
			$perpage = 10;
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$children = $_forum_post->get_list($start, $perpage, "AND a.FORUM_TOPICID = '{$id}'");
			foreach ($children as $key => $child) {
				$child['USERNAME'] = $child['USERNAME'] ? $child['USERNAME'] : $GLOBALS['lang']['bbs.topic.guest'];
				if($child['FIRST']) $topic = $_forum_topic->format($topic, $child);
				
				$children[$key] = $child;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/bbs/topic/_view&id={$id}&ppage={$_var[gp_ppage]}&ppsize={$_var[gp_ppsize]}{$search[querystring]}", $perpage, false);
		}
		
		if($_var['gp_formsubmit']){
			$post = $_forum_post->get_by_id($_var['gp_postid']);
			
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_view.validate.username']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['bbs.topic_view.validate.title']."<br/>";
			if(empty($_var['gp_txtContent'])) $_var['msg'] .=  $GLOBALS['lang']['bbs.topic_view.validate.content']."<br/>";
			
			if(empty($_var['msg'])){
				$post_user = $_user->get_by_id($_var['gp_txtUserName']);
				
				if($post_user == null) $post_user = $_user->get_by_mobile($_var['gp_txtUserName']);
				if($post_user == null) $post_user = $_user->get_by_email($_var['gp_txtUserName']);
				if($post_user == null) $post_user = $_user->get_by_name($_var['gp_txtUserName']);
				
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				
				if($_var['gp_do'] == 'post'){
					$_forum_post->insert(array(
					'TITLE' => $_var['gp_txtTitle'],
					'CONTENT' => $_var['gp_txtContent'], 
					'FORUMID' => $forum['FORUMID'], 
					'FORUM_TOPICID' => $topic['FORUM_TOPICID'], 
					'ADDRESS' => $_var['clientip'], 
					'USERID' => $post_user ? $post_user['USERID'] : 0,
					'USERNAME' => $post_user ? ($post_user['WX_FANSID'] ? $post_user['REALNAME'] : $post_user['USERNAME']) : $_var['gp_txtUserName'], 
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					$post_count = $_forum_post->get_count("AND a.FORUM_TOPICID = '{$topic[FORUM_TOPICID]}' AND a.FIRST = 0");
					$_forum_topic->update($topic['FORUM_TOPICID'], array('POSTCOUNT' => $post_count));
					
					$_log->insert($GLOBALS['lang']['bbs.topic.post.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['bbs.topic']);
					
					show_message($GLOBALS['lang']['bbs.topic.post.message.add'], "{ADMIN_SCRIPT}/bbs/topic/_view&id={$id}&page=100000&ppage={$_var[gp_ppage]}&ppsize={$_var[gp_ppsize]}{$search[querystring]}");
				}elseif($_var['gp_do'] == 'edit' && $post){
					$_forum_post->update($post['FORUM_POSTID'], array(
					'TITLE' => $_var['gp_txtTitle'],
					'CONTENT' => $_var['gp_txtContent'], 
					'ADDRESS' => $_var['clientip'], 
					'USERNAME' => $post_user ? ($post_user['WX_FANSID'] ? $post_user['REALNAME'] : $post_user['USERNAME']) : $_var['gp_txtUserName'], 
					));
					
					$_log->insert($GLOBALS['lang']['bbs.topic.post.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['bbs.topic']);
					
					show_message($GLOBALS['lang']['bbs.topic.post.message.update'], "{ADMIN_SCRIPT}/bbs/topic/_view&id={$id}&page={$_var[page]}&ppage={$_var[gp_ppage]}&ppsize={$_var[gp_ppsize]}{$search[querystring]}");
				}
			}
		}
		
		include_once view('/module/bbs/view/topic_view');
	}
	
	//JSON
	public function _json(){
		global $_var;
		
		$_forum_post = new _forum_post();
		
		$post = null;
		$id = $_var['gp_id'] + 0;
		if($id > 0) $post = $_forum_post->get_by_id($id);
		
		exit_json($post);
	}
	
	//移动
	public function _move(){
		global $_var;
		
		$_forum = new _forum();
		$forum_list = $_forum->get_list(0, 0);
		
		include_once view('/module/bbs/view/topic_move');
	}
	
	//上传图片
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
		
		$file_limit = $_var['gp_limit'] + 0;
		$file_uploaded = $_var['gp_uploaded'] + 0;
		
		if($file_limit > 0 && $file_limit < $file_uploaded + 1) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.limit']."{$file_limit}".$GLOBALS['lang']['admin.validate.swfupload.echo.limit.pic']);
		
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$upload->init($_FILES['Filedata'], 'mutual');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach) {
				$tempimgsize = getimagesize('attachment/'.$upload->attach['target']);
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|0'.'|'.$tempimgsize[0].'|'.$tempimgsize[1]);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
}
?>