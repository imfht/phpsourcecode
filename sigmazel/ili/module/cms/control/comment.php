<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\control;

use admin\model\_log;
use cms\model\_comment;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/cms/lang.php';

/**
 * 文章评论
 * @author sigmazel
 * @since v1.0.2
 */
class comment{
    /**
     * 默认
     */
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_comment = new _comment();
		
		//搜索结果
		$search = $_comment->search();
		
		if($_var['gp_do'] == 'delete'){
			$tempdata = $_comment->get_by_id($_var['gp_id'] + 0);
			if($tempdata){
				$_comment->delete($tempdata['COMMENTID']);
				
				$_log->insert($GLOBALS['lang']['cms.comment.log.delete']."({$tempdata[COMMENTID]})", $GLOBALS['lang']['cms.comment']);
			}
		}
		
		if($_var['gp_do'] == 'pass'){
			$tempdata = $_comment->get_by_id($_var['gp_id'] + 0);
			if($tempdata){
				$_comment->update($tempdata['COMMENTID'], array('ISAUDIT' => 1));
				
				$_log->insert($GLOBALS['lang']['cms.comment.log.pass']."({$tempdata[COMMENTID]})", $GLOBALS['lang']['cms.comment']);
			}
		}
		
		if($_var['gp_do'] == 'fail'){
			$tempdata = $_comment->get_by_id($_var['gp_id'] + 0);
			if($tempdata){
				$_comment->update($tempdata['COMMENTID'], array('ISAUDIT' => 0));
				
				$_log->insert($GLOBALS['lang']['cms.comment.log.fail']."({$tempdata[COMMENTID]})", $GLOBALS['lang']['cms.comment']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$commentids = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_comment->get_by_id($val);
				if($tempdata){
					$_comment->delete($tempdata['COMMENTID']);
					
					$commentids .= $tempdata['COMMENTID'].'， ';
				}
			}
			
			if($commentids) $_log->insert($GLOBALS['lang']['cms.comment.log.delete.list']."(".$commentids.")", $GLOBALS['lang']['cms.comment']);
		}
		
		if($_var['gp_do'] == 'pass_list' && is_array($_var['gp_cbxItem'])){
			$commentids = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_comment->get_by_id($val);
				if($tempdata){
					$_comment->update($tempdata['COMMENTID'], array('ISAUDIT' => 1));
					
					$commentids .= $tempdata['COMMENTID'].'， ';
				}
			}
			
			if($commentids) $_log->insert($GLOBALS['lang']['cms.comment.log.pass.list']."(".$commentids.")", $GLOBALS['lang']['cms.comment']);
		}
		
		if($_var['gp_do'] == 'fail_list' && is_array($_var['gp_cbxItem'])){
			$commentids = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_comment->get_by_id($val);
				if($tempdata){
					$_comment->update($tempdata['COMMENTID'], array('ISAUDIT' => 0));
					
					$commentids .= $tempdata['COMMENTID'].'， ';
				}
			}
			
			if($commentids) $_log->insert($GLOBALS['lang']['cms.comment.log.fail.list']."(".$commentids.")", $GLOBALS['lang']['cms.comment']);
		}
		
		$count = $_comment->get_count_of_article($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$comments = $_comment->get_list_of_article($start, $perpage, $search['wheresql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/cms/comment{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/cms/view/comment');
	}
	
	/**
	 * 修改
	 */
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_comment = new _comment();

		$search = $_comment->search();

		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/cms/comment");
		
		$comment = $_comment->get_by_id($id);
		if($comment == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/cms/comment");
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			if(empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['cms.comment_edit.validate.content']."<br/>";
			
			if(empty($_var['msg'])){
				$_comment->update($id, array(
				'CONTENT' => utf8substr($_var['gp_txtContent'], 0, 200), 
				'REPLY' => utf8substr($_var['gp_txtContent'], 0, 200)
				));
				
				$_log->insert($GLOBALS['lang']['cms.comment.log.update']."({$_var[gp_id]})", $GLOBALS['lang']['cms.comment']);
				
				show_message($GLOBALS['lang']['cms.comment.message.update'], "{ADMIN_SCRIPT}/cms/comment&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/cms/view/comment_edit');
	}
}
?>