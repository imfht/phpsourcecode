<?php
//版权所有(C) 2014 www.ilinei.com

namespace book\control;

use admin\model\_log;
use book\model\_book;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/book/lang.php';

/**
 * 报名
 * @author sigmazel
 * @since v1.0.2
 */
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_book = new _book();
		
		$book_status = $_book->get_status();
		$search = $_book->search();
		
		if($_var['gp_do'] == 'delete'){
			$book = $_book->get_by_id($_var['gp_id']);
			if($book){
				$_book->delete($book['BOOKID']);
				
				$_log->insert($GLOBALS['lang']['book.index.log.delete']."({$book[TITLE]})", $GLOBALS['lang']['book.index']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$book_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$book = $_book->get_by_id($val);
				if($book){
					$_book->delete($book['BOOKID']);
					$book_titles .= $book['TITLE'].'， ';
				}
			}
			
			if($book_titles) $_log->insert($GLOBALS['lang']['book.index.log.delete.list']."({$book_titles})", $GLOBALS['lang']['book.index']);
		}
		
		if($_var['gp_do'] == 'pass_list' && is_array($_var['gp_cbxItem'])){
			$book_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$book = $_book->get_by_id($val);
				if($book){
					$_book->update($book['BOOKID'], array('STATUS' => 1));
					$book_titles .= $book['TITLE'].'， ';
				}
				
				unset($book);
			}
			
			if($book_titles) $_log->insert($GLOBALS['lang']['book.index.log.pass.list']."({$book_titles})", $GLOBALS['lang']['book.index']);
		}
		
		if($_var['gp_do'] == 'fail_list' && is_array($_var['gp_cbxItem'])){
			$book_titles = '';
			
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$book = $_book->get_by_id($val);
				if($book){
					$_book->update($book['BOOKID'], array('STATUS' => 0));
				}
			}
			
			if($book_titles) $_log->insert($GLOBALS['lang']['book.index.log.fail.list']."({$book_titles})", $GLOBALS['lang']['book.index']);
		}
		
		$books = array();
		$count = $_book->get_count();
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$books = $_book->get_list($start, $perpage);
			foreach ($books as $key => $book){
				$book['_STATUS'] = $book['STATUS'];
				$book['STATUS'] = $book_status[$book['STATUS']];
				$books[$key] = $book;
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/book{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/book/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_book = new _book();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtRealName'])) $_var['msg'] .= $GLOBALS['lang']['book.index_edit.validate.realname']."<br/>";
			if(empty($_var['gp_txtConnect'])) $_var['msg'] .= $GLOBALS['lang']['book.index_edit.validate.connect']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['book.index_edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtRealName'] = utf8substr($_var['gp_txtRealName'], 0, 20);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 50);
				$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 100);
				$_var['gp_txtDepartment'] = utf8substr($_var['gp_txtDepartment'], 0, 50);
				$_var['gp_txtPlace'] = utf8substr($_var['gp_txtPlace'], 0, 50);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				$_var['gp_txtReply'] = utf8substr($_var['gp_txtReply'], 0, 100);
				
				$_book->insert(array(
				'REALNAME' => $_var['gp_txtRealName'], 
				'CONNECT' => $_var['gp_txtConnect'], 
				'EMAIL' => $_var['gp_txtEmail'], 
				'DEPARTMENT' => $_var['gp_txtDepartment'], 
				'PLACE' => $_var['gp_txtPlace'], 
				'TITLE' => $_var['gp_txtTitle'], 
				'ADDRESS' => $_var['clientip'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'REPLY' => $_var['gp_txtReply'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['book.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['book.index']);
				
				show_message($GLOBALS['lang']['book.index.message.add'], "{ADMIN_SCRIPT}/book");
			}
		}
		
		include_once view('/module/book/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_book = new _book();

        $search = $_book->search();

		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/book");
		
		$book = $_book->get_by_id($id);
		if($book == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/book"); 
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtRealName'])) $_var['msg'] .= $GLOBALS['lang']['book.index_edit.validate.realname']."<br/>";
			if(empty($_var['gp_txtConnect'])) $_var['msg'] .= $GLOBALS['lang']['book.index_edit.validate.connect']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['book.index_edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtRealName'] = utf8substr($_var['gp_txtRealName'], 0, 20);
				$_var['gp_txtConnect'] = utf8substr($_var['gp_txtConnect'], 0, 50);
				$_var['gp_txtEmail'] = utf8substr($_var['gp_txtEmail'], 0, 100);
				$_var['gp_txtDepartment'] = utf8substr($_var['gp_txtDepartment'], 0, 50);
				$_var['gp_txtPlace'] = utf8substr($_var['gp_txtPlace'], 0, 50);
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				$_var['gp_txtReply'] = utf8substr($_var['gp_txtReply'], 0, 100);
				
				$_book->update($book['BOOKID'], array(
				'REALNAME' => $_var['gp_txtRealName'], 
				'CONNECT' => $_var['gp_txtConnect'], 
				'EMAIL' => $_var['gp_txtEmail'], 
				'DEPARTMENT' => $_var['gp_txtDepartment'], 
				'PLACE' => $_var['gp_txtPlace'], 
				'TITLE' => $_var['gp_txtTitle'], 
				'ADDRESS' => $_var['clientip'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'REPLY' => $_var['gp_txtReply'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['book.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['book.index']);
				
				show_message($GLOBALS['lang']['book.index.message.update'], "{ADMIN_SCRIPT}/book&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/book/view/index_edit');
	}
	
	//导出excel
	public function _excel(){
		global $dispatches;
		
		$_book = new _book();
        $book_status = $_book->get_status();

		$books = array();

        if(empty($dispatches['operations']['export'])){
            $books = $_book->get_list(0, -1);
            foreach($books as $key => $book){
                $book['_STATUS'] = $book['STATUS'];
                $book['STATUS'] = $book_status[$book['STATUS']];
                $books[$key] = $book;
            }
        }

		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".$GLOBALS['lang']['book.index.excel.title'].".xls");
		
		include_once view('/module/book/view/index_excel');
		exit(0);
	}
	
}
?>