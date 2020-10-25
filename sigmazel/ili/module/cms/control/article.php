<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\control;

use admin\model\_log;
use admin\model\_table;
use admin\model\_menu;
use admin\model\_district;
use cms\model\_category;
use cms\model\_article;
use cms\model\_article_content;
use cms\model\_subject;
use cms\model\_keyword;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/cms/lang.php';
//引入繁简转换函数
require_once ROOTPATH.'/source/function/convert.php';

//文章
class article{
	//默认
	public function index(){
		include_once view('/module/cms/view/article_iframe');
	}
	
	//左边树
	public function _ztree(){
		global $_var;
		
		$_category = new _category();
		
		if($_var['current']['USERID'] == -1) $categories = $_category->get_all(0, 'article', false);
		else $categories = $_category->get_list_of_user("AND a.TYPE = 'article' AND b.USERID = {$_var[current][USERID]}");
		
		foreach($categories as $key => $category){
			$categories[$key]['CNAME'] = str_replace('\'', '‘', $category['CNAME']);
		}
		
		include_once view('/module/cms/view/article_ztree');
	}
	
	//列表
	public function _list(){
		global $_var;
		
		$_log = new _log();
		$_category = new _category();
		$_article = new _article();
		$_article_content = new _article_content();
		$_subject = new _subject();
		
		$subjects = $_subject->get_all(1);
		$search = $_article->search();
		
		if($_var['gp_do'] == 'delete'){
			$tempdata = $_article->get_by_id($_var['gp_id']);
			if($tempdata){
				$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => -1));
				
				$_log->insert($GLOBALS['lang']['cms.article.log.delete']."({$tempdata[TITLE]})", $GLOBALS['lang']['cms.article']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$article_titles = '';
			foreach ($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				if($tempdata){
					$_article->update($tempdata['ARTICLEID'], array('ISAUDIT' => -1));
						
					$article_titles .= $tempdata['TITLE'].'，';
				}
				
				unset($tempdata);
			}
			
			if($article_titles) $_log->insert($GLOBALS['lang']['cms.article.log.delete.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['gp_do'] == 'move_list' && is_array($_var['gp_cbxItem'])){
			$move_category = $_category->get_by_id($_var['gp_hdnMoveCategoryID'] + 0);
			if($move_category){
				$article_titles = '';
				foreach ($_var['gp_cbxItem'] as $key => $val){
					$tempdata = $_article->get_by_id($val);
					if($tempdata){
						$_article->update($tempdata['ARTICLEID'], array('CATEGORYID' => $move_category['CATEGORYID']));
							
						$article_titles .= $tempdata['TITLE'].'，';
					}
				
					unset($tempdata);
				}
				
				if($article_titles) $_log->insert($GLOBALS['lang']['cms.article.log.move.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
			}
		}
		
		if($_var['gp_do'] == 'copy_list' && is_array($_var['gp_cbxItem'])){
			$copy_category = $_category->get_by_id($_var['gp_hdnCopyCategoryID'] + 0);
			if($copy_category){
				$article_titles = '';
				
				foreach($_var['gp_cbxItem'] as $key => $val){
					$tempdata = $_article->get_by_id($val, 0);
					
					$tmpdata = array();
					$tmpdata['CONTENT'] = $tempdata['CONTENT'];
					
					if($tempdata){
						$tempdata['CATEGORYID'] = $copy_category['CATEGORYID'];
						
						$convert_type = $_var['gp_hdnConvertType'] + 0;
						if($convert_type == 0) {
							$tempdata['TITLE'] = zh2cn($tempdata['TITLE']);
							$tempdata['SUBTITLE'] = zh2cn($tempdata['SUBTITLE']);
							$tempdata['AUTHOR'] = zh2cn($tempdata['AUTHOR']);
							$tempdata['ADDRESS'] = zh2cn($tempdata['ADDRESS']);
							$tempdata['SUMMARY'] = zh2cn($tempdata['SUMMARY']);
							$tempdata['KEYWORDS'] = zh2cn($tempdata['KEYWORDS']);
							
							$tmpdata['CONTENT'] = zh2cn($tempdata['CONTENT']);
						}elseif($convert_type == 1){
							$tempdata['TITLE'] = cn2zh($tempdata['TITLE']);
							$tempdata['SUBTITLE'] = cn2zh($tempdata['SUBTITLE']);
							$tempdata['AUTHOR'] = cn2zh($tempdata['AUTHOR']);
							$tempdata['ADDRESS'] = cn2zh($tempdata['ADDRESS']);
							$tempdata['SUMMARY'] = cn2zh($tempdata['SUMMARY']);
							$tempdata['KEYWORDS'] = cn2zh($tempdata['KEYWORDS']);
							
							$tmpdata['CONTENT'] = cn2zh($tempdata['CONTENT']);
						}
						
						$content = $tempdata['CONTENT'];
						
						unset($tempdata['ARTICLEID']);
						unset($tempdata['CNAME']);
						unset($tempdata['IDENTITY']);
						unset($tempdata['CONTENT']);
						unset($tempdata['COLUMNS']);
						unset($tempdata['CATEGORY_ISAUDIT']);
						
						$articleid = $_article->insert($tempdata);
						$_article_content->insert(array(
						'ARTICLEID' => $articleid, 
						'CONTENT' => $content
						));
						
						$article_titles .= $tempdata['TITLE'].'，';
					}
					
					unset($articleid);
					unset($content);
					unset($tempdata);
					unset($tempdata);
				}
				
				if($article_titles) $_log->insert($GLOBALS['lang']['cms.article.log.copy.list']."({$article_titles})", $GLOBALS['lang']['cms.article']);
			}
		}
		
		if($_var['gp_do'] == 'thumb_list' && is_array($_var['gp_cbxItem'])){
			$cimage = new image();
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				if($tempdata){
					for($i = 1; $i <= 20; $i++) {
						$tcolumn = 'FILE'.sprintf('%02d', $i);
						
						if($tempdata[$tcolumn]){
							$tempsrc = $tempdata[$tcolumn][4];
							$tempdata[$tcolumn] = explode('|', $tempdata[$tcolumn][4]);
							$tempdata[$tcolumn][2] = thumb_image($cimage, $tempdata[$tcolumn][0]);
							$tempfile = implode('|', $tempdata[$tcolumn]);
							
							$_article->update($val, array($tcolumn => $tempfile));
						}
						
						unset($tempfile);
						unset($tempsrc);
						unset($tcolumn);
					}
				}
		
				unset($tempdata);
			}
			
			$_log->insert($GLOBALS['lang']['cms.article.log.thumb.list'], $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['gp_do'] == 'send_list' && is_array($_var['gp_cbxItem'])){
			$send_subject = $_subject->get_by_id($_var['gp_hdnSubjectID'] + 0);
			$article_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_article->get_by_id($val);
				if($tempdata){
					$_article->update($tempdata['ARTICLEID'], array('SUBJECTID' => $send_subject ? $send_subject['SUBJECTID'] : 0));
	
					$article_titles .= $tempdata['TITLE'].'，';
				}
					
				unset($tempdata);
			}
			
			if($article_titles) $_log->insert($GLOBALS['lang']['cms.article.log.send.list.0']."({$article_titles})".$GLOBALS['lang']['cms.article.log.send.list.1'], $GLOBALS['lang']['cms.article']);
		}
		
		if($_var['current']['USERID'] > 0) $search['wheresql'] .= " AND a.CATEGORYID IN(SELECT CATEGORYID FROM tbl_user_category WHERE USERID = '{$_var[current][USERID]}')";
		
		$count = $_article->get_count_of_join("AND a.ISAUDIT > -1 {$search[wheresql]}");
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$articles = $_article->get_list_of_join($start, $perpage, "AND a.ISAUDIT > -1 {$search[wheresql]}");
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/cms/article/_list{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/cms/view/article_list');
	}
	
	//移动
	public function _move(){
		global $_var;
		
		include_once view('/module/cms/view/article_move');
	}
	
	//发送
	public function _send(){
		global $_var;
		
		$_subject = new _subject();
		$subjects = $_subject->get_all(1);
		
		include_once view('/module/cms/view/article_send');
	}
	
	//复制
	public function _copy(){
		global $_var;
		
		include_once view('/module/cms/view/article_copy');
	}
	
	//分类
	public function _category(){
		global $_var;
		
		$_category = new _category();
		
		$json = array('DATA' => array(), 'CATEGORY' => array());
		
		$parentid = $_var['gp_parentid'] + 0;
		
		if($parentid > 0) {
			$json['CATEGORY'] = $_category->get_by_id($parentid);
			$json['CATEGORY'] = $_category->format($json['CATEGORY']);
		}
		
		$categories = array();
		
		if($_var['current']['USERID'] > 0){
			$categories = $_category->get_list_of_user("AND a.TYPE = 'article' AND a.PARENTID = {$parentid} AND b.USERID = {$_var[current][USERID]}");
		}else{
			$categories = $_category->get_list(0, 0, "AND a.TYPE = 'article' AND a.PARENTID = {$parentid}");
		}
		
		foreach($categories as $key => $category){
			$json['DATA'][] = $category;
		}
		
		exit_json($json);
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_subject = new _subject();
		$_category = new _category();
		$_article = new _article();
		$_article_content = new _article_content();
		$_keyword = new _keyword();
		
		$table = $_table->get_by_identity('article');
		$subjects = $_subject->get_all(1);
		
		$cid = $_var['gp_cid'] + 0;
		if($cid > 0){
			$category = $_category->get_by_id($cid);
			$category = $_category->format($category);
		}
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$category = $_category->get_by_id($_var['gp_hdnCategoryID'] + 0);
		
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtPubdate']) && !is_datetime($_var['gp_txtPubdate'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.pubdate']."<br/>";
			if(!empty($_var['gp_txtExpried']) && !is_shortdate($_var['gp_txtExpried'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.expried']."<br/>";
			if(!$category) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.category'];
			
			$category = $_category->format($category);
			
			if(($category['COLUMNS']['CONTENT']['type'] == 0 || $category['COLUMNS']['CONTENT']['type'] == 1) && empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.content']."<br/>";
			
			if(empty($_var['msg'])){
				if(empty($_var['gp_txtPubdate'])) $_var['gp_txtPubdate'] = date('Y-m-d H:i:s');
				else $_var['gp_txtPubdate'] .= ":00";
				
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 100);
				$_var['gp_txtSummary'] = utf8substr($_var['gp_txtSummary'], 0, 200);
				$_var['gp_txtAuthor'] = utf8substr($_var['gp_txtAuthor'], 0, 50);
				$_var['gp_txtAddress'] = utf8substr($_var['gp_txtAddress'], 0, 50);
				$_var['gp_txtLink'] = utf8substr($_var['gp_txtLink'], 0, 200);
				$_var['gp_txtKeywords'] = utf8substr($_var['gp_txtKeywords'], 0, 50);
					
				$_var['gp_txtSubTitle1'] = utf8substr($_var['gp_txtSubTitle1'], 0, 80);
				$_var['gp_txtSubTitle2'] = utf8substr($_var['gp_txtSubTitle2'], 0, 80);
				$_var['gp_txtSubTitle3'] = utf8substr($_var['gp_txtSubTitle3'], 0, 80);
				
				$_var['gp_hdnModule'] = utf8substr($_var['gp_hdnModule'], 0, 200);
				
				$content = $_var['gp_txtContent'];
				
				$_var['gp_txtSummary'] = $category['COLUMNS']['SUMMARY']['show'] ? strip2words($_var['gp_txtSummary'], false) : utf8substr(strip2words($content), 0, 200);
				$_var['gp_txtSummary'] = substr($_var['gp_txtSummary'], -1) == '\\' ? substr($_var['gp_txtSummary'], 0, -1) : $_var['gp_txtSummary'];
				
				if($category['FILES']['show']) $article_file_arr = file_upload_images($table['FILENUM']);
				else $article_file_arr = $_article->pick($table['FILENUM'], $_POST['txtContent']);
				
				if($category['COLUMNS']['CONTENT']['type'] == 2) $content = $_var['gp_hdnFilePath'];
				
				$articleid = $_article->insert(array_merge(array(
				'CATEGORYID' => $category['CATEGORYID'],
				'TITLE' => $_var['gp_txtTitle'],
				'SUBTITLE' => $_var['gp_txtSubTitle1'].'|'.$_var['gp_txtSubTitle2'].'|'.$_var['gp_txtSubTitle3'],
				'AUTHOR' => $_var['gp_txtAuthor'],
				'LINK' => $_var['gp_txtLink'],
				'ADDRESS' => $_var['gp_txtAddress'],
				'SUMMARY' => $_var['gp_txtSummary'],
				'KEYWORDS' => $_var['gp_txtKeywords'],
				'ISAUDIT' => $category['ISAUDIT'] ? 0 : 1,
				'ISTOP' => $_var['gp_rdoIsTop'] + 0,
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'CREDIT' => $_var['gp_txtCredit'] + 0,
				'EXPRIED' => $_var['gp_txtExpried'] ? $_var['gp_txtExpried'] : null,
				'SUBJECTID' => $_var['gp_sltSubjectId'] + 0,
				'PUBDATE' => $_var['gp_txtPubdate'],
				'PICKIMAGE' => $category['FILES']['show'] ? 0 : 1, 
				'PICKSUMMARY' => $category['COLUMNS']['SUMMARY']['show'] ? 0 : 1, 
				'TYPE' => $category['COLUMNS']['CONTENT']['type'] + 0, 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'],
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'MODULE' => $_var['gp_hdnModule']
				), $article_file_arr));
				
				if($articleid){
					$_article_content->insert(array(
					'ARTICLEID' => $articleid, 
					'CONTENT' => $content
					));
					
					$_keyword->save($_var['gp_txtKeywords']);
					
					$_log->insert($GLOBALS['lang']['cms.article_edit.log.pub']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['cms.article']);
					
					show_message($GLOBALS['lang']['cms.article_edit.message.pub'], "{ADMIN_SCRIPT}/cms/article/_list&cid={$_var[gp_cid]}");
				}
				
				show_message($GLOBALS['lang']['cms.article_edit.message.error'], "{ADMIN_SCRIPT}/cms/article/_list&cid={$_var[gp_cid]}");
			}
		}
		
		include_once view('/module/cms/view/article_pub');
	}
	
	//修改
	public function _update(){
		global $_var, $dispatches;
		
		$_log = new _log();
		$_table = new _table();
		$_subject = new _subject();
		$_category = new _category();
		$_article = new _article();
		$_article_content = new _article_content();
		$_keyword = new _keyword();

        $search = $_article->search();

		$table = $_table->get_by_identity('article');
		$subjects = $_subject->get_all(1);
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['cms.article.update.error.message'], "{ADMIN_SCRIPT}/cms/{$dispatches[control]}/_list&cid={$_var[gp_cid]}&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
		
		$article = $_article->get_by_id($id, 0);
		if($article == null) show_message($GLOBALS['lang']['cms.article.update.error.message'], "{ADMIN_SCRIPT}/cms/{$dispatches[control]}/_list&cid={$_var[gp_cid]}&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
		
		if($article['TYPE'] == 2){
			$article = format_row_file($article, 'CONTENT');
			$article['FILE'] = $article['CONTENT'];
			$article['CONTENT'] = '';
		}
		
		!$article['MODULE'] && $article['MODULE'] = 'empty';
		
		if($article['MODULE'] != 'empty' && !strexists($article['MODULE'], 'empty')){
			$tmparr = explode('|', $article['MODULE']);
			if(count($tmparr) > 2) $article['MODULE_TIPS'] = $tmparr[count($tmparr) - 1];
		}
		
		!$article['MODULE_TIPS'] && $article['MODULE_TIPS'] = $GLOBALS['lang']['cms.module.view.empty.label'];
		
		$subject = $_subject->get_by_id($article['SUBJECTID']);
		$category = $_category->get_by_id($article['CATEGORYID']);
		$category = $_category->format($category);
		
		$article['EXPRIED'] = $article['EXPRIED'] > 0 ? date('Y-m-d', strtotime($article['EXPRIED'])) : '';
		$article['PUBDATE'] = $article['PUBDATE'] > 0 ? date('Y-m-d H:i', strtotime($article['PUBDATE'])) : '';
		
		$article = format_row_files($article);
		$article_files = $_article->get_files($article, $table['FILENUM']);
		
		$subtitles = explode('|', $article['SUBTITLE']);
		$article['SUBTITLE1'] = $subtitles[0];
		$article['SUBTITLE2'] = $subtitles[1];
		$article['SUBTITLE3'] = $subtitles[2];
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			$category = $_category->get_by_id($_var['gp_hdnCategoryID'] + 0);
		
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtPubdate']) && !is_datetime($_var['gp_txtPubdate'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.pubdate']."<br/>";
			if(!empty($_var['gp_txtExpried']) && !is_shortdate($_var['gp_txtExpried'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.expried']."<br/>";
			if(!$category) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.category'];
			
			$category = $_category->format($category);
			
			if(($category['COLUMNS']['CONTENT']['type'] == 0 || $category['COLUMNS']['CONTENT']['type'] == 1) && empty($_var['gp_txtContent'])) $_var['msg'] .= $GLOBALS['lang']['cms.article_edit.validate.content']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 100);
				$_var['gp_txtSummary'] = utf8substr($_var['gp_txtSummary'], 0, 200);
				$_var['gp_txtAuthor'] = utf8substr($_var['gp_txtAuthor'], 0, 50);
				$_var['gp_txtAddress'] = utf8substr($_var['gp_txtAddress'], 0, 50);
				$_var['gp_txtLink'] = utf8substr($_var['gp_txtLink'], 0, 200);
				$_var['gp_txtKeywords'] = utf8substr($_var['gp_txtKeywords'], 0, 50);
					
				$_var['gp_txtSubTitle1'] = utf8substr($_var['gp_txtSubTitle1'], 0, 80);
				$_var['gp_txtSubTitle2'] = utf8substr($_var['gp_txtSubTitle2'], 0, 80);
				$_var['gp_txtSubTitle3'] = utf8substr($_var['gp_txtSubTitle3'], 0, 80);
				
				$_var['gp_hdnModule'] = utf8substr($_var['gp_hdnModule'], 0, 200);
				
				$content = $_var['gp_txtContent'];
				
				$_var['gp_txtSummary'] = $category['COLUMNS']['SUMMARY']['show'] ? strip2words($_var['gp_txtSummary'], false) : utf8substr(strip2words($content), 0, 200);
				$_var['gp_txtSummary'] = substr($_var['gp_txtSummary'], -1) == '\\' ? substr($_var['gp_txtSummary'], 0, -1) : $_var['gp_txtSummary'];
				
				if($category['FILES']['show']) $article_file_arr = file_upload_images($table['FILENUM']);
				else $article_file_arr = $_article->pick($table['FILENUM'], $_POST['txtContent']);
				
				if($category['COLUMNS']['CONTENT']['type'] == 2) $content = $_var['gp_hdnFilePath'];
				
				$_article->update($article['ARTICLEID'], array_merge(array(
				'CATEGORYID' => $category['CATEGORYID'],
				'TITLE' => $_var['gp_txtTitle'],
				'SUBTITLE' => $_var['gp_txtSubTitle1'].'|'.$_var['gp_txtSubTitle2'].'|'.$_var['gp_txtSubTitle3'],
				'AUTHOR' => $_var['gp_txtAuthor'],
				'LINK' => $_var['gp_txtLink'],
				'ADDRESS' => $_var['gp_txtAddress'],
				'SUMMARY' => $_var['gp_txtSummary'],
				'KEYWORDS' => $_var['gp_txtKeywords'],
				'ISAUDIT' => $category['ISAUDIT'] ? 0 : 1,
				'ISTOP' => $_var['gp_rdoIsTop'] + 0,
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'CREDIT' => $_var['gp_txtCredit'] + 0,
				'EXPRIED' => $_var['gp_txtExpried'] ? $_var['gp_txtExpried'] : null,
				'SUBJECTID' => $_var['gp_sltSubjectId'] + 0, 
				'PUBDATE' => $_var['gp_txtPubdate'],
				'PICKIMAGE' => $category['FILES']['show'] ? 0 : 1, 
				'PICKSUMMARY' => $category['COLUMNS']['SUMMARY']['show'] ? 0 : 1, 
				'TYPE' => $category['COLUMNS']['CONTENT']['type'] + 0, 
				'USERNAME' => $_var['current']['USERNAME'],
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'MODULE' => $_var['gp_hdnModule']
				), $article_file_arr));
				
				$_article_content->update($article['ARTICLEID'], array('CONTENT' => $content));
				
				$_keyword->save($_var['gp_txtKeywords']);
				
				$_log->insert($GLOBALS['lang']['cms.article_edit.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['cms.article']);
				
				show_message($GLOBALS['lang']['cms.article_edit.message.update'], "{ADMIN_SCRIPT}/cms/{$dispatches[control]}/_list&cid={$_var[gp_cid]}&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/cms/view/article_update');
	}
	
	//查看
	public function _view(){
		global $_var, $dispatches;
		
		$_table = new _table();
		$_subject = new _subject();
		$_category = new _category();
		$_article = new _article();

		$search = $_article->search();

		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['cms.article.update.error.message'], "{ADMIN_SCRIPT}/cms/{$dispatches[control]}/_list&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
		
		$article = $_article->get_by_id($id, 0);
		if($article == null) show_message($GLOBALS['lang']['cms.article.update.error.message'], "{ADMIN_SCRIPT}/cms/{$dispatches[control]}/_list&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
		
		if($article['TYPE'] == 1) $article['CONTENT'] = nl2br($article['CONTENT']);
		elseif($article['TYPE'] == 2){
			$article = format_row_file($article, 'CONTENT');
			$article['FILE'] = $article['CONTENT'];
			$article['CONTENT'] = '';
		}
		
		!$article['MODULE'] && $article['MODULE'] = 'empty';
		
		if($article['MODULE'] != 'empty' && !strexists($article['MODULE'], 'empty')){
			$tmparr = explode('|', $article['MODULE']);
			
			if(count($tmparr) > 2) $module_tips = $tmparr[count($tmparr) - 1];
			else $module_tips = $GLOBALS['lang']['cms.module.view.empty.label'];
		}
		
		$table = $_table->get_by_identity('article');
		$subject = $_subject->get_by_id($article['SUBJECTID']);
		$category = $_category->get_by_id($article['CATEGORYID']);
		$category = $_category->format($category);
		
		$article['EXPRIED'] = $article['EXPRIED'] > 0 ? date('Y-m-d', strtotime($article['EXPRIED'])) : '';
		$article['PUBDATE'] = $article['PUBDATE'] > 0 ? date('Y-m-d', strtotime($article['PUBDATE'])) : '';
		$article['CONTENT'] = str_replace('<embed src="attachment/', '<embed src="../attachment/', $article['CONTENT']);
		
		$article = format_row_files($article);
		$article = format_row_flv($article, 'CONTENT');
		$article = format_row_mp3($article, 'CONTENT', 'pc');
		
		$article_files = $_article->get_files($article, $table['FILENUM']);
		
		include_once view('/module/cms/view/article_view');
	}
	
	//上传
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
		
		$file_limit = $_var['gp_limit'] + 0;
		$file_uploaded = $_var['gp_uploaded'] + 0;
		
		if($file_limit > 0 && $file_limit < $file_uploaded + 1) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.limit']."{$file_limit}".$GLOBALS['lang']['admin.validate.swfupload.echo.limit.pic']);
		
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$cimage = new image();
			
			$upload->init($_FILES['Filedata'], 'cms');
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			
			if($_var['gp_type'] != 'file' && !$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach) {
				if($_var['gp_type'] != 'file'){
					$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
					$thumb = thumb_image($cimage, $upload->attach['target']);
				}else{
					$thumb = 0;
				}
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$temp_img_size[0].'|'.$temp_img_size[1]);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
	//模块
	public function _module(){
		global $_var;
		
		$_menu = new _menu();
		
		//默认空和地图模块
		$modules[] = array('NAME' => $GLOBALS['lang']['cms.article_edit.view.module.dialog.list.empty'], 'IDENTITY' => 'empty');
		$modules[] = array('NAME' => $GLOBALS['lang']['cms.article_edit.view.module.dialog.list.map'], 'IDENTITY' => 'map');
		
		$menuids = array();
		$menu_list = array();
		
		$menu_list['note'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/note');
		$menu_list['record'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/note/record');
		$menu_list['poll'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/poll');
		$menu_list['exam'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/exam');
		$menu_list['album'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/album');
		$menu_list['join'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/join');
		$menu_list['book'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/book');
		$menu_list['subscribe'] = $_menu->get_by_url('{$ADMIN_SCRIPT}/subscribe');
		
		foreach($menu_list as $key => $menu){
			$menu && $menuids[] = $menu['MENUID'];
		}
		
		//当前用户可查看的模块菜单列表
		$user_menus = $_menu->get_list_of_user($_var['current'], "AND m.MENUID IN(".eimplode($menuids).")");
		
		$menus = array();
		foreach($user_menus as $key => $user_menu){
			foreach($menu_list as $key => $menu){
				if($menu['MENUID'] == $user_menu['MENUID']){
					$menus[$key] = $menu;
					break;
				}
			}
		}
		
		//如果可查看留言记录即也可使用留言板模块 
		if($menus['note'] && $menus['record']) unset($menus['record']);
		if($menus['record']){
			$menus['note'] = $menus['record'];
			unset($menus['record']);
		}
		
		//加载模块语言包
		foreach($menus as $key => $menu){
			$modules[] = array('NAME' => $menu['CNAME'], 'IDENTITY' => $key);
			
			require_once ROOTPATH."/module/{$key}/lang.php";
		}
		
		//获得当前的模块
		$module = '';
		
		if($_var['gp_module']){
			$tmparr = explode('|', $_var['gp_module']);
			
			foreach ($modules as $key => $item){
				if($tmparr[0] == $item['IDENTITY']){
					$module = $tmparr[0];
					break;
				}
			}
		}
		
		if(empty($module)) $module = $modules[0]['IDENTITY'];
		
		if($module == 'empty' || $module == 'map') $class = '\cms\model\_article';
		else $class = $module.'\model\_'.$module;
		
		$model = new $class();
		
		if($_var['gp_do'] == 'extend'){
			if(!$_var['gp_module']) exit_echo('');
			
			if($module == 'empty') include_once view('/module/cms/view/module_empty');
			elseif(method_exists($model, 'module')) $model->module($_var['gp_module']);
			else echo '';
			
			exit(0);
		}
		
		include_once view('/module/cms/view/article_module');
	}
	
	//地图
	public function _module_map(){
		global $_var;
		
		$_district = new _district();
		
		$provinces = $_district->get_children(0);
		
		$provinceid = $_var['gp_provinceid'] + 0;
		if($provinceid == 0) $provinceid = 1643;
		
		$cities = $_district->get_children($provinceid);
		
		$cityid = $_var['gp_cityid'] + 0;
		if($cityid == 0) $cityid = 1644;
		
		$maps = array('lat' => 32.041750, 'lng' => 118.784158, 'zoom' => 12);
		
		$tmparr = explode(',', $_var['gp_latLngz']);
		
		if(count($tmparr) > 0) $maps['lat'] = $tmparr[0];
		if(count($tmparr) > 1) $maps['lng'] = $tmparr[1];
		if(count($tmparr) > 2) $maps['zoom'] = $tmparr[2];
		
		include_once view('/module/cms/view/module_map');
	}
	
	//市
	public function _city(){
		global $_var;
		
		$_district = new _district();
		
		exit_json($_district->get_children($_var['gp_provinceid'] + 0));
	}
}
?>