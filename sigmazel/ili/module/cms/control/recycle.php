<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\control;

use admin\model\_log;
use admin\model\_table;
use cms\model\_article;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/cms/lang.php';

//回收站
class recycle{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_article = new _article();
		
		$table = $_table->get_by_identity('article');
		$search = $_article->search();
		
		if($_var['gp_do'] == 'delete'){
			$tempdata = $_article->get_by_id($_var['gp_id'] + 0);
			if($tempdata){
				$_article->delete($tempdata['ARTICLEID']);
				
				file_clear($tempdata, $table['FILENUM']);
				
				$_log->insert($GLOBALS['lang']['cms.article_recycle.log.delete']."({$tempdata[TITLE]})", $GLOBALS['lang']['cms.article']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$article_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				
				if($tempdata){
					$_article->delete($tempdata['ARTICLEID']);
					
					file_clear($tempdata, $table['FILENUM']);
					
					$article_titles .= $tempdata['TITLE'].'，';
				}
				
				unset($tempdata);
			}
			
			if($article_titles) $_log->insert($GLOBALS['lang']['cms.article_recycle.log.delete.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['gp_do'] == 'restore'){
			$tempdata = $_article->get_by_id($_var['gp_id'] + 0);
			if($tempdata){
				$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => $tempdata['ISAUDIT'] == 1 ? 0 : 1));
				
				$_log->insert($GLOBALS['lang']['cms.article_recycle.log.restore']."({$tempdata[TITLE]})", $GLOBALS['lang']['cms.article']);
			}
		}
		
		if($_var['gp_do'] == 'restore_list' && is_array($_var['gp_cbxItem'])){
			$article_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				
				if($tempdata){
					$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => $tempdata['ISAUDIT'] == 1 ? 0 : 1));
					
					$article_titles .= $tempdata['TITLE'].'，';
				}
				
				unset($tempdata);
			}
			
			if($article_titles) $_log->insert($GLOBALS['lang']['cms.article_recycle.log.restore.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['current']['USERID'] > 0) $search['wheresql'] .= " AND a.CATEGORYID IN(SELECT CATEGORYID FROM tbl_user_category WHERE USERID = '{$_var[current][USERID]}')";
		
		$count = $_article->get_count_of_join("AND a.ISAUDIT = -1 {$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$articles = $_article->get_list_of_join($start, $perpage, "AND a.ISAUDIT = -1 {$search[wheresql]}");
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/cms/recycle{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/cms/view/recycle');
	}
	
	//兼容修改返回
	public function _list(){
		$this->index();
	}
	
	//调用article查看
	public function _view(){
		$article = new article();
		$article->_view();
	}
	
	
}

?>