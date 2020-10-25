<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\control;

use admin\model\_log;
use cms\model\_article;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/cms/lang.php';

//待审核
class audit{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_article = new _article();
		
		$search = $_article->search();
		
		if($_var['gp_do'] == 'delete'){
			$tempdata = $_article->get_by_id($_var['gp_id']);
			if($tempdata){
				$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => -1));
				
				$_log->insert($GLOBALS['lang']['cms.article_audit.log.delete']."({$tempdata[TITLE]})", $GLOBALS['lang']['cms.article']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$article_titles = '';
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				if($tempdata){
					$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => -1));
						
					$article_titles .= $tempdata['TITLE'].', ';
				}
				
				unset($tempdata);
			}
			
			if($article_titles) $_log->insert($GLOBALS['lang']['cms.article_audit.log.delete.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['gp_do'] == 'pass_list' && is_array($_var['gp_cbxItem'])){
			$article_titles = '';
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				if($tempdata){
					$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => 1));
					
					$article_titles .= $tempdata['TITLE'].', ';
				}
				
				unset($tempdata);
			}
			
			if($article_titles) $_log->insert($GLOBALS['lang']['cms.article_audit.log.pass.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['current']['USERID'] > 0) $search['wheresql'] .= " AND a.CATEGORYID IN(SELECT CATEGORYID FROM tbl_user_category WHERE USERID = '{$_var[current][USERID]}')";
		
		$count = $_article->get_count_of_join("AND a.ISAUDIT = 0 {$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$articles = $_article->get_list_of_join($start, $perpage, "AND a.ISAUDIT = 0 {$search[wheresql]}");
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/cms/audit{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/cms/view/audit');
	}
	
	//兼容修改返回
	public function _list(){
		$this->index();
	}
	
	//调用article修改
	public function _update(){
		$article = new article();
		$article->_update();
	}
	
	//调用article查看
	public function _view(){
		$article = new article();
		$article->_view();
	}
	
	//调用article分类 
	public function _category(){
		$article = new article();
		$article->_category();
	}
	
	//调用article上传
	public function _upload(){
		$article = new article();
		$article->_upload();
	}
	
	//调用article模块
	public function _module(){
		$article = new article();
		$article->_module();
	}
	
	//调用article地图
	public function _module_map(){
		$article = new article();
		$article->_module_map();
	}
	
	//调用article市
	public function _city(){
		$article = new article();
		$article->_city();
	}
}
?>