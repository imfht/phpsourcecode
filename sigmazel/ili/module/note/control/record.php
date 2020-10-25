<?php
//版权所有(C) 2014 www.ilinei.com

namespace note\control;

use admin\model\_log;
use note\model\_note;
use note\model\_note_record;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/note/lang.php';

//留言列表
class record{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();
		$_note_record = new _note_record();
		
		$note_list = $_note->get_list(0, 0);
		$search = $_note_record->search();
		
		if($_var['gp_do'] == 'delete'){
			$record = $_note_record->get_by_id($_var['gp_id']);
			if($record){
				$_note_record->delete($record['NOTE_RECORDID']);
				
				$_log->insert($GLOBALS['lang']['note.record.log.delete']."({$record[TITLE]})", $GLOBALS['lang']['note.record']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$record_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$record = $_note_record->get_by_id($val);
				if($record){
					$_note_record->delete($record['NOTE_RECORDID']);
					
					$record_titles . $record['TITLE'].'， ';
				}
				
				unset($record);
			}
			
			if($record_titles) $_log->insert($GLOBALS['lang']['note.record.log.delete.list']."({$record_titles})", $GLOBALS['lang']['note.record']);
		}
		
		if($_var['gp_do'] == 'open_list' && is_array($_var['gp_cbxItem'])){
			$record_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$record = $_note_record->get_by_id($val);
				if($record){
					$_note_record->update($record['NOTE_RECORDID'], array('ISOPEN' => 1));
					$record_titles . $record['TITLE'].'， ';
				}
				
				unset($record);
			}
			
			if($record_titles) $_log->insert($GLOBALS['lang']['note.record.log.open.list']."({$record_titles})", $GLOBALS['lang']['note.record']);
		}
		
		if($_var['gp_do'] == 'hide_list' && is_array($_var['gp_cbxItem'])){
			$record_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$record = $_note_record->get_by_id($val);
				if($record){
					$_note_record->update($record['NOTE_RECORDID'], array('ISOPEN' => 0));
					$record_titles . $record['TITLE'].'， ';
				}
			}
			
			if($record_titles) $_log->insert($GLOBALS['lang']['note.record.log.hide.list']."({$record_titles})", $GLOBALS['lang']['note.record']);
		}
		
		if($_var['gp_do'] == 'move_list' && is_array($_var['gp_cbxItem'])){
			$movenote = $_note->get_by_id($_var['gp_hdnMoveNoteID'] + 0);
			if($movenote){
				$record_titles = '';
				
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$record = $_note_record->get_by_id($val);
					if($record){
						$_note_record->update($record['NOTE_RECORDID'], array('NOTEID' => $movenote['NOTEID']));
						$_note_record->update_batch("PARENTID = '{$record[NOTE_RECORDID]}'", array('NOTEID' => $movenote['NOTEID']));
						
						$record_titles . $record['TITLE'].'， ';
					}
					
					unset($record);
				}
				
				if($record_titles) $_log->insert($GLOBALS['lang']['note.record.log.move.list']."({$record_titles})", $GLOBALS['lang']['note.record']);
			}
		}
		
		$count = $_note_record->get_count("AND a.PARENTID = 0 {$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$records = $_note_record->get_list($start, $perpage, "AND a.PARENTID = 0 {$search[wheresql]}");
			foreach ($records as $key => $record){
				$record['USERNAME'] = $record['USERNAME'] ? $record['USERNAME'] : $GLOBALS['lang']['note.record.guest'];
				
				$record['_ISOPEN'] = $record['ISOPEN'];
				$record['ISOPEN'] = $record['ISOPEN'] ? $GLOBALS['lang']['admin.operation.open'] : $GLOBALS['lang']['admin.operation.hide'];
				$record['REPLYS'] = $_note_record->get_count("AND a.PARENTID = '{$record[NOTE_RECORDID]}'");
				
				$records[$key] = $record;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/note/record{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/note/view/record');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();
		$_note_record = new _note_record();
		
		$note_list = $_note->get_list(0, 0);
		$search = $_note_record->search();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$noteid = $_var['gp_sltNoteId'] + 0;
			$cnote = $noteid > 0 ? $_note->get_by_id($noteid) : null;
			
			if(!$cnote) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.note']."<br/>";
			else {
				$cnote['NEEDS'] = unserialize($cnote['NEEDS']);
				if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.title']."<br/>";
				
				if($cnote['GUEST'] == 0 && empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.username']."<br/>";
				if($cnote['NEEDS']['department'] && empty($_var['gp_txtDepartment'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.department']."<br/>";
				if($cnote['NEEDS']['place'] && empty($_var['gp_txtPlace'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.place']."<br/>";
				if($cnote['NEEDS']['email'] && empty($_var['gp_txtEmail'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.email']."<br/>";
				if($cnote['NEEDS']['connect'] && empty($_var['gp_txtConnect'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.connect']."<br/>";
				
				if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.content']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtDepartment'] = utf8substr($_var['gp_txtDepartment'], 0, 50);
				$_var['gp_txtPlace'] = utf8substr($_var['gp_txtPlace'], 0, 100);
				$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 100);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 100);
				$_var['gp_txtKeywords'] = utf8substr($_var['gp_txtKeywords'], 0, 50);
				
				$_note_record->insert(array(
				'TITLE' => $_var['gp_txtTitle'],
				'DEPARTMENT' => $_var['gp_txtDepartment'], 
				'PLACE' => $_var['gp_txtPlace'], 
				'EMAIL' => $_var['gp_txtEmail'], 
				'CONNECT' => $_var['gp_txtConnect'], 
				'CONTENT' => strip_tags($_var['gp_txtContent']), 
				'REPLY' => strip_tags($_var['gp_txtReply']), 
				'NOTEID' => $cnote['NOTEID'],
				'ISOPEN' => $_var['gp_rdoIsOpen'] + 0, 
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0, 
				'KEYWORDS' => $_var['gp_txtKeywords'],
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['gp_txtUserName'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['note.record.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['note.record']);
				
				show_message($GLOBALS['lang']['note.record.message.add'], "{ADMIN_SCRIPT}/note/record");
			}
		}
		
		include_once view('/module/note/view/record_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();
		$_note_record = new _note_record();
		
		$note_list = $_note->get_list(0, 0);
		$search = $_note_record->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/note/record");
		
		$record = $_note_record->get_by_id($id);
		if($record == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/note/record"); 
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$noteid = $_var['gp_sltNoteId'] + 0;
			$cnote = $noteid > 0 ? $_note->get_by_id($noteid) : null;
			
			if(!$cnote) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.note']."<br/>";
			else {
				$cnote['NEEDS'] = unserialize($cnote['NEEDS']);
				
				if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.title']."<br/>";
				
				if($cnote['GUEST'] == 0 && empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.username']."<br/>";
				if($cnote['NEEDS']['department'] && empty($_var['gp_txtDepartment'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.department']."<br/>";
				if($cnote['NEEDS']['place'] && empty($_var['gp_txtPlace'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.place']."<br/>";
				if($cnote['NEEDS']['email'] && empty($_var['gp_txtEmail'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.email']."<br/>";
				if($cnote['NEEDS']['connect'] && empty($_var['gp_txtConnect'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.connect']."<br/>";
				
				if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.content']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtDepartment'] = utf8substr($_var['gp_txtDepartment'], 0, 50);
				$_var['gp_txtPlace'] = utf8substr($_var['gp_txtPlace'], 0, 100);
				$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 100);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 100);
				$_var['gp_txtKeywords'] = utf8substr($_var['gp_txtKeywords'], 0, 50);
				
				$_note_record->update($record['NOTE_RECORDID'], array(
				'TITLE' => $_var['gp_txtTitle'],
				'DEPARTMENT' => $_var['gp_txtDepartment'], 
				'PLACE' => $_var['gp_txtPlace'], 
				'EMAIL' => $_var['gp_txtEmail'], 
				'CONNECT' => $_var['gp_txtConnect'], 
				'CONTENT' => strip_tags($_var['gp_txtContent']), 
				'REPLY' => strip_tags($_var['gp_txtReply']), 
				'NOTEID' => $cnote['NOTEID'],
				'ISOPEN' => $_var['gp_rdoIsOpen'] + 0, 
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'KEYWORDS' => $_var['gp_txtKeywords'],
				'USERNAME' => $_var['gp_txtUserName'], 
				));
				
				$_log->insert($GLOBALS['lang']['note.record.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['note.record']);
				
				show_message($GLOBALS['lang']['note.record.message.update'], "{ADMIN_SCRIPT}/note/record&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/note/view/record_edit');
	}
	
	//查看
	public function _view(){
		global $_var;
		
		$_log = new _log();
		$_note = new _note();
		$_note_record = new _note_record();
		
		$search = $_note_record->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/note/record");
		
		$record = $_note_record->get_by_id($id);
		if($record == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/note/record"); 
		
		$record['USERNAME'] = $record['USERNAME'] ? $record['USERNAME'] : $GLOBALS['lang']['note.record.guest'];
		$record['CONTENT'] = nl2br($record['CONTENT']);
		$record['REPLY'] = nl2br($record['REPLY']);
		
		$cnote = $_note->get_by_id($record['NOTEID']);
		$cnote['NEEDS'] = unserialize($cnote['NEEDS']);
		
		if($_var['gp_do'] == 'delete'){
			$drecord = $_note_record->get_by_id($_var['gp_recordid']);
			if($drecord){
				$_note_record->delete($drecord['NOTE_RECORDID']);
				
				$_log->insert($GLOBALS['lang']['note.record.reply.log.delete']."({$drecord[TITLE]})", $GLOBALS['lang']['note.record']);
			}
		}
		
		$count = $_note_record->get_count("AND a.PARENTID = '{$id}'");
		if($count){
			$perpage = 10;
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$children = $_note_record->get_list($start, $perpage, "AND a.PARENTID = '{$id}'");
			foreach ($children as $key => $child){
				$child['USERNAME'] = $child['USERNAME'] ? $child['USERNAME'] : $GLOBALS['lang']['note.record.guest'];
				$child['CONTENT'] = nl2br($child['CONTENT']);
				
				$children[$key] = $child;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/note/record/_view&id={$id}&ppage={$_var[gp_ppage]}&ppsize={$_var[gp_ppsize]}{$search[querystring]}", $perpage, false);
		}
		
		if($_var['gp_formsubmit']){
			$erecord = $_note_record->get_by_id($_var['gp_recordid']);
			
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.title']."<br/>";
			
			if($cnote['GUEST'] == 0 && empty($_var['gp_txtUserName'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.username']."<br/>";
			if($cnote['NEEDS']['department'] && empty($_var['gp_txtDepartment'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.department']."<br/>";
			if($cnote['NEEDS']['place'] && empty($_var['gp_txtPlace'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.place']."<br/>";
			if($cnote['NEEDS']['email'] && empty($_var['gp_txtEmail'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.email']."<br/>";
			if($cnote['NEEDS']['connect'] && empty($_var['gp_txtConnect'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.connect']."<br/>";
			
			if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['note.record_edit.validate.reply']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 20);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtDepartment'] = utf8substr($_var['gp_txtDepartment'], 0, 50);
				$_var['gp_txtPlace'] = utf8substr($_var['gp_txtPlace'], 0, 100);
				$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 100);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 100);
				
				if($_var['gp_do'] == 'reply'){
					$_note_record->insert(array(
					'TITLE' => $_var['gp_txtTitle'],
					'DEPARTMENT' => $_var['gp_txtDepartment'], 
					'PLACE' => $_var['gp_txtPlace'], 
					'EMAIL' => $_var['gp_txtEmail'], 
					'CONNECT' => $_var['gp_txtConnect'], 
					'CONTENT' => strip_tags($_var['gp_txtContent']), 
					'REPLY' => '', 
					'NOTEID' => $cnote['NOTEID'],
					'ISOPEN' => 1, 
					'PARENTID' => $id, 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['gp_txtUserName'], 
					'EDITTIME' => date('Y-m-d H:i:s')
					));
					
					$_log->insert($GLOBALS['lang']['note.record.reply.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['note.record']);
					
					show_message($GLOBALS['lang']['note.record.reply.message.add'], "{ADMIN_SCRIPT}/note/record/_view&id={$id}&page=100000&ppage={$_var[gp_ppage]}&ppsize={$_var[gp_ppsize]}{$search[querystring]}");
				}elseif($_var['gp_do'] == 'edit' && $erecord){
					$_note_record->update($erecord['NOTE_RECORDID'], array(
					'TITLE' => $_var['gp_txtTitle'],
					'DEPARTMENT' => $_var['gp_txtDepartment'], 
					'PLACE' => $_var['gp_txtPlace'], 
					'EMAIL' => $_var['gp_txtEmail'], 
					'CONNECT' => $_var['gp_txtConnect'], 
					'CONTENT' => strip_tags($_var['gp_txtContent']), 
					'USERNAME' => $_var['gp_txtUserName'], 
					));
					
					$_log->insert($GLOBALS['lang']['note.record.reply.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['note.record']);
					
					show_message($GLOBALS['lang']['note.record.reply.message.update'], "{ADMIN_SCRIPT}/note/record/_view&id={$id}&page={$_var[page]}&ppage={$_var[gp_ppage]}&ppsize={$_var[gp_ppsize]}{$search[querystring]}");
				}
			}
		}
		
		include_once view('/module/note/view/record_view');
	}
	
	//JSON
	public function _json(){
		global $_var;
		
		$_note_record = new _note_record();
		
		$record = null;
		
		$id = $_var['gp_id'] + 0;
		if($id > 0) $record = $_note_record->get_by_id($id);
		
		exit_json($record);
	}
	
	//移动
	public function _move(){
		$_note = new _note();
		
		$note_list = $_note->get_list(0, 0);
		
		include_once view('/module/note/view/record_move');
	}
}
?>