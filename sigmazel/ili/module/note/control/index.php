<?php
//版权所有(C) 2014 www.ilinei.com

namespace note\control;

use admin\model\_log;
use note\model\_note;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/note/lang.php';

//留言板
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();
		
		$search = $_note->search();
		
		if(is_array($_var['gp_identity'])){
			foreach ($_var['gp_identity'] as $key => $val){
				$_note->update($key, array('DISPLAYORDER' => $_var['gp_displayorder'][$key]));
			}
		}
		
		if($_var['gp_do'] == 'delete'){
			$note = $_note->get_by_id($_var['gp_id']);
			if($note){
				$_note->delete($note['NOTEID']);
				
				$_log->insert($GLOBALS['lang']['note.index.log.delete']."({$note[TITLE]})", $GLOBALS['lang']['note.index']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$note_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$note = $_note->get_by_id($val);
				if($note){
					$_note->delete($note['NOTEID']);
					
					$note_titles .= $note['TITLE'].'， ';
				}
				
				unset($note);
			}
			
			if($note_titles) $_log->insert($GLOBALS['lang']['note.index.log.delete.list']."({$note_titles})", $GLOBALS['lang']['note.index']);
		}
		
		$count = $_note->get_count();
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$notes = $_note->get_list($start, $perpage);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/note{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/note/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['note.index_edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				
				$_note->insert(array(
				'TITLE' => $_var['gp_txtTitle'],
				'REMARK' => $_var['gp_txtRemark'], 
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
				'GUEST' => $_var['gp_rdoGuest'] + 0, 
				'REPLY' => $_var['gp_rdoReply'] + 0,
				'NEEDS' => serialize($_var['gp_cbxNeeds']),
				'BEGINDATE' => $_var['gp_txtBeginDate'], 
				'ENDDATE' => $_var['gp_txtEndDate'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['note.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['note.index']);
				
				show_message($GLOBALS['lang']['note.index.message.add'], "{ADMIN_SCRIPT}/note");
			}
		}
		
		include_once view('/module/note/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();

        $search = $_note->search();

		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/note");
		
		$note = $_note->get_by_id($id);
		if($note == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/note"); 
		
		$note['BEGINDATE'] = $note['BEGINDATE'] > 0 ? date('Y-m-d', strtotime($note['BEGINDATE'])) : '';
		$note['ENDDATE'] = $note['ENDDATE'] > 0 ? date('Y-m-d', strtotime($note['ENDDATE'])) : '';
		$note['NEEDS'] = unserialize($note['NEEDS']);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['note.index_edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				
				$_note->update($note['NOTEID'], array(
				'TITLE' => $_var['gp_txtTitle'],
				'REMARK' => $_var['gp_txtRemark'], 
				'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0, 
				'GUEST' => $_var['gp_rdoGuest'] + 0, 
				'REPLY' => $_var['gp_rdoReply'] + 0,
				'NEEDS' => serialize($_var['gp_cbxNeeds']),
				'BEGINDATE' => $_var['gp_txtBeginDate'], 
				'ENDDATE' => $_var['gp_txtEndDate'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['note.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['note.index']);
				
				show_message($GLOBALS['lang']['note.index.message.update'], "{ADMIN_SCRIPT}/note&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/note/view/index_edit');
	}
	
}
?>