<?php
//版权所有(C) 2014 www.ilinei.com

namespace join\control;

use admin\model\_log;
use join\model\_join;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/join/lang.php';

//加盟
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_join = new _join();
		
		$join_status = $_join->get_status();
		$search = $_join->search();
		
		if($_var['gp_do'] == 'delete'){
			$join = $_join->get_by_id($_var['gp_id']);
			if($join && $join['STATUS'] < 2){
				$_join->delete($join['JOINID']);
				
				$_log->insert($GLOBALS['lang']['join.index.log.delete']."({$join[TITLE]})", $GLOBALS['lang']['join.index']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$join_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$join = $_join->get_by_id($val);
				if($join && $join['STATUS'] < 2){
					$_join->delete($join['JOINID']);
					
					$join_titles .= $join['TITLE'].'， ';
				}
			}
			
			if($join_titles) $_log->insert($GLOBALS['lang']['join.index.log.delete.list']."({$join_titles})", $GLOBALS['lang']['join.index']);
		}
		
		if($_var['gp_do'] == 'deal_list' && is_array($_var['gp_cbxItem'])){
			$join_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$join = $_join->get_by_id($val);
				if($join){
					$_join->update($join['JOINID'], array('STATUS' => 2));
					
					$join_titles .= $join['TITLE'].'， ';
				}
				
				unset($join);
			}
			
			if($join_titles) $_log->insert($GLOBALS['lang']['join.index.log.deal.list']."({$join_titles})", $GLOBALS['lang']['join.index']);
		}
		
		if($_var['gp_do'] == 'dealing_list' && is_array($_var['gp_cbxItem'])){
			$join_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$join = $_join->get_by_id($val);
				if($join){
					$_join->update($join['JOINID'], array('STATUS' => 1));
					
					$join_titles .= $join['TITLE'].'， ';
				}
			}
			
			if($join_titles) $_log->insert($GLOBALS['lang']['join.index.log.dealing.list']."({$join_titles})", $GLOBALS['lang']['join.index']);
		}
		
		$joins = array();
		$count = $_join->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$join_list = $_join->get_list($start, $perpage, $search['wheresql']);
			foreach ($join_list as $key => $join){
				$join['_STATUS'] = $join['STATUS'];
				$join['STATUS'] = $join_status[$join['STATUS']];
				
				$joins[] = $join;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/join{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/join/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_join = new _join();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['join.index_edit.validate.title']."<br/>";
			if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['join.index_edit.validate.content']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 100);
				$_var['gp_txtUserName'] = $_var['gp_txtUserName'] ? $_var['gp_txtUserName'] : $_var['current']['USERNAME'];
				$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 30);
				
				$_join->insert(array(
				'TITLE' => $_var['gp_txtTitle'], 
				'CONNECT' => $_var['gp_txtConnect'], 
				'ADDRESS' => $_var['clientip'], 
				'AGENT' => cutstr($_SERVER['HTTP_USER_AGENT'], 200, ''), 
				'CONTENT' => $_var['gp_txtContent'], 
				'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 200), 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['gp_txtUserName'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['join.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['join.index']);
				
				show_message($GLOBALS['lang']['join.index.message.add'], "{ADMIN_SCRIPT}/join");
			}
		}
		
		include_once view('/module/join/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_join = new _join();

        $search = $_join->search();

		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/join");
		
		$join = $_join->get_by_id($id);
		if($join == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/join"); 
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['join.index_edit.validate.title']."<br/>";
			if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['join.index_edit.validate.content']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 100);
				$_var['gp_txtUserName'] = $_var['gp_txtUserName'] ? $_var['gp_txtUserName'] : $_var['current']['USERNAME'];
				$_var['gp_txtUserName'] = utf8substr($_var['gp_txtUserName'], 0, 30);
				
				$_join->update($join['JOINID'], array(
				'TITLE' => $_var['gp_txtTitle'], 
				'CONNECT' => $_var['gp_txtConnect'], 
				'ADDRESS' => $_var['clientip'], 
				'AGENT' => cutstr($_SERVER['HTTP_USER_AGENT'], 200, ''), 
				'CONTENT' => $_var['gp_txtContent'], 
				'REMARK' => utf8substr($_var['gp_txtRemark'], 0, 200), 
				'USERNAME' => $_var['gp_txtUserName'],
				'OPERATOR' => $_var['current']['USERNAME'], 
				'OPERATETIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['join.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['join.index']);
				
				show_message($GLOBALS['lang']['join.index.message.update'], "{ADMIN_SCRIPT}/join&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/join/view/index_edit');
	}
	
	//查看
	public function _view(){
		global $_var;
		
		$_join = new _join();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/join");
		
		$join = $_join->get_by_id($id);
		if($join == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/join"); 
		
		include_once view('/module/join/view/index_view');
	}
}
?>