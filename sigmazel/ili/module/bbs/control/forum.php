<?php
//版权所有(C) 2014 www.ilinei.com

namespace bbs\control;

use admin\model\_log;
use admin\model\_manager;
use user\model\_group;
use user\model\_user;
use bbs\model\_forum;
use bbs\model\_forum_user;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/bbs/lang.php';

//讨论版
class forum{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_manager = new _manager();
		
		$_group = new _group();
		$_forum = new _forum();
		
		$groups = $_group->get_all();
		$search = $_forum->search();
		
		if(is_array($_var['gp_forumid'])){
			foreach ($_var['gp_forumid'] as $key => $val){
				$_forum->update($key, array('DISPLAYORDER' => $_var['gp_displayorder'][$key]));
			}
		}
		
		if($_var['gp_do'] == 'delete'){
			$forum = $_forum->get_by_id($_var['gp_id']);
			if($forum){
				$_forum->delete($forum['FORUMID']);
				
				$_log->insert($GLOBALS['lang']['bbs.forum.log.delete']."({$forum[NAME]})", $GLOBALS['lang']['bbs.forum']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$forum_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$forum = $_forum->get_by_id($val);
				if($forum){
					$_forum->delete($forum['FORUMID']);
					
					$forum_titles .= $forum['NAME'].', ';
				}
				
				unset($forum);
			}
			
			if($forum_titles) $_log->insert($GLOBALS['lang']['bbs.forum.log.delete.list']."({$forum_titles})", $GLOBALS['lang']['bbs.forum']);
		}
		
		$count = $_forum->get_count();
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$forums = array();
			$forum_list = $_forum->get_list($start, $perpage);
			foreach ($forum_list as $key => $forum){
				$forum = $_forum->get_stat($forum);
				
				$group_names = '';
				$tmparr = explode(',', $forum['GROUP']);
				foreach($groups as $key => $group){
					if(in_array($key, $tmparr)) $group_names .= $group['CNAME'].'；';
				}
				
				$forum['GROUPS'] = $group_names ? $group_names : $GLOBALS['lang']['bbs.forum.view.td.groups.empty'];
				
				if($forum['MANAGER']){
					$managers = '';
					$tmparr = explode(',', $forum['MANAGER']);
					$manager_list = $_manager->get_list(0, 0, "AND m.USERID IN(".eimplode($tmparr).")");
					foreach ($manager_list as $key => $manager){
						$managers .= $manager['USERNAME'].'；';
					}
					
					$forum['MANAGER'] = $managers;
				}
				
				!$forum['MANAGER'] && $forum['MANAGER'] = $GLOBALS['lang']['bbs.forum.view.td.manager.empty'];
				
				$forums[] = $forum;
				
				unset($tmparr);
				unset($managers);
				unset($manager_list);
				unset($group_names);
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/bbs/forum{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/bbs/view/forum');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_user = new _user();
		$_group = new _group();
		$_forum = new _forum();
		$_forum_user = new _forum_user();
		
		$groups = $_group->get_all();
		$search = $_forum->search();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtName'])) $_var['msg'] .= $GLOBALS['lang']['bbs.forum_edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtName'] = utf8substr($_var['gp_txtName'], 0, 50);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				$_var['gp_txtManager'] = utf8substr($_var['gp_txtManager'], 0, 50);
				
				$forumid = $_forum->insert(array(
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0, 
				'NAME' => $_var['gp_txtName'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'RULE' => $_var['gp_txtRule'], 
				'ISAUDIT' => $_var['gp_rdoIsAudit'] + 0,
				'GUEST' => $_var['gp_rdoGuest'] + 0,
				'MANAGER' => $_var['gp_txtManager'], 
				'GROUP' => implode(',', $_var['gp_cbxGroup']).'', 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'].''
				));
				
				$userids = array_flip(array_flip($_var['gp_txtUserID']));
				foreach($userids as $key => $val){
					if(!$val) continue;
					
					$user = $_user->get_by_id($val);
					if(!$user) continue;
					
					$_forum_user->insert(array(
					'USERID' => $user['USERID'], 
					'USERNAME' => $user['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'FORUMID' => $forumid
					));
					
					unset($user);
				}
				
				$_log->insert($GLOBALS['lang']['bbs.forum.log.add']."({$_var[gp_txtName]})", $GLOBALS['lang']['bbs.forum']);
				
				show_message($GLOBALS['lang']['bbs.forum.message.add'], "{ADMIN_SCRIPT}/bbs/forum");
			}
		}
		
		include_once view('/module/bbs/view/forum_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_user = new _user();
		$_group = new _group();
		$_forum = new _forum();
		$_forum_user = new _forum_user();
		
		$groups = $_group->get_all();
		$search = $_forum->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/bbs/forum");
		
		$forum = $_forum->get_by_id($id);
		if($forum == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/bbs/forum"); 
		
		$forum['GROUPS'] = array();
		$temparr = explode(',', $forum['GROUP']);
		foreach ($temparr as $key => $val){
			if($groups[$val]) $forum['GROUPS'][$val] = 1;
		}
		
		$forum_users = $_forum_user->get_list(0, 0);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtName'])) $_var['msg'] .= $GLOBALS['lang']['bbs.forum_edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtName'] = utf8substr($_var['gp_txtName'], 0, 50);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				$_var['gp_txtManager'] = utf8substr($_var['gp_txtManager'], 0, 50);
				
				$_forum->update($forum['FORUMID'], array(
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0, 
				'NAME' => $_var['gp_txtName'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'RULE' => $_var['gp_txtRule'], 
				'ISAUDIT' => $_var['gp_rdoIsAudit'] + 0,
				'GUEST' => $_var['gp_rdoGuest'] + 0,
				'MANAGER' => $_var['gp_txtManager'], 
				'GROUP' => implode(',', $_var['gp_cbxGroup']), 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01']
				));
				
				$_forum_user->delete("FORUMID = '{$forum[FORUMID]}'");
				
				$userids = array_flip(array_flip($_var['gp_txtUserID']));
				foreach($userids as $key => $val){
					if(!$val) continue;
					
					$user = $_user->get_by_id($val);
					if(!$user) continue;
					
					$_forum_user->insert(array(
					'USERID' => $user['USERID'], 
					'USERNAME' => $user['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'FORUMID' => $forum['FORUMID']
					));
					
					unset($user);
				}
				
				$_log->insert($GLOBALS['lang']['bbs.forum.log.update']."({$_var[gp_txtName]})", $GLOBALS['lang']['bbs.forum']);
				
				show_message($GLOBALS['lang']['bbs.forum.message.update'], "{ADMIN_SCRIPT}/bbs/forum&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/bbs/view/forum_edit');
	}
	
	//检查用户
	public function _user(){
		global $_var;
		
		$_user = new _user();
		
		if(empty($_var['gp_username'])) exit_echo("<tr id=\"error\"><td colspan=\"3\"><font color=red>".$GLOBALS['lang']['bbs.forum_edit.validate.guest.users.empty']."</font></td></tr>");
		
		$user = null;
		if(is_mobile($_var['gp_username'])) $user = $_user->get_by_mobile($_var['gp_username']);
		elseif(is_email($_var['gp_username'])) $user = $_user->get_by_email($_var['gp_username']);
		elseif(is_cint($_var['gp_username'])) $user = $_user->get_by_id($_var['gp_username']);
		
		if(!$user) exit_echo("<tr id=\"error\"><td colspan=\"3\"><font color=red>".$GLOBALS['lang']['bbs.forum_edit.validate.guest.users.error']."</font></td></tr>"); 
		if(!$user['ISMANAGER'] && $user['ISAUDIT']) exit_echo("<tr id=\"error\"><td colspan=\"3\"><font color=red>".$GLOBALS['lang']['bbs.forum_edit.validate.guest.users.isauit']."</font></td></tr>"); 
		
		include_once view('/module/bbs/view/forum_user');
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
			
			if($upload->attach){
				$tempimgsize = getimagesize('attachment/'.$upload->attach['target']);
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|0|'.$tempimgsize[0].'|'.$tempimgsize[1].'|'.$_var['gp_file']);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
}
?>