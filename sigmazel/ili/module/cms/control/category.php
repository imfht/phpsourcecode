<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\control;

use admin\model\_log;
use cms\model\_category;
use user\model\_user_category;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/cms/lang.php';

//分类
class category{
	//默认
	public function index(){
		global $_var, $setting;
		
		$_log = new _log();
		$_category = new _category();
		
		$wheresql = 'AND a.PARENTID = 0 ';
		
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0){
			$parent = $_category->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['CATEGORYID'] : 0;
			
			if($_var['gp_parentid']){
				$crumbs = $_category->get_crumbs($_var['gp_parentid']);
				$wheresql = "AND a.PARENTID = '{$_var[gp_parentid]}'";
			}
		}
		
		if(is_array($_var['gp_identity'])){
			foreach($_var['gp_identity'] as $key => $val){
				$_category->update($key, array(
				    'IDENTITY' => $val, 
				    'DISPLAYORDER' => $_var['gp_displayorder'][$key]
				));
			}
			
			cache_delete('category_article');
		}
		
		if($_var['gp_do'] == 'delete'){
			$category = $_category->get_by_id($_var['gp_id']);
			if($category){
				$_category->delete($category['CATEGORYID']);
				
				$category = format_row_files($category);
				file_clear($category, 2);
				
				if($parent){
					if($parent['CHILDREN'] > 0){
						$parent['CHILDREN'] = $parent['CHILDREN'] - 1;
						
						$_category->update($parent['CATEGORYID'], array('CHILDREN' => $parent['CHILDREN']));
					}
				}
				
				cache_delete('category_article');
				
				$_log->insert($GLOBALS['lang']['cms.category.log.delete']."({$category[CNAME]})", $GLOBALS['lang']['cms.category']);
			}
		}elseif($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$category_names = '';
			$category_count = 0;
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$category = $_category->get_by_id($val);
				if(!$category) continue;
				
				$_category->delete($category['CATEGORYID']);
				
				$category = format_row_files($category);
				file_clear($category, 2);
				
				$category_names .= $category['CNAME'].',';
				$category_count++;
				
				unset($category);
			}
			
			if($parent){
				if($parent['CHILDREN'] > 0){
					$parent['CHILDREN'] = $parent['CHILDREN'] - $category_count;
					
					$_category->update($parent['CATEGORYID'], array('CHILDREN' => $parent['CHILDREN']));
				}
			}
			
			cache_delete('category_article');
			
			if($category_names) $_log->insert($GLOBALS['lang']['cms.category.log.delete.list']."({$category_names})", $GLOBALS['lang']['cms.category']);
		}
		
		if($_var['current']['USERID'] > 0)  $wheresql .= "AND a.CATEGORYID IN(SELECT CATEGORYID FROM tbl_user_category WHERE USERID = '{$_var[current][USERID]}')";
		
		$categories = array();
		$count = $_category->get_count("AND a.TYPE = 'article' {$wheresql}");
		if($count){
			$category_list = $_category->get_list(0, 0, "AND a.TYPE = 'article' {$wheresql}");
			foreach ($category_list as $key => $category){
				$category = $_category->format($category);
				
				//格式化显示自定义属性
				$category['_COLUMNS'] = $category['COLUMNS'];
				$category['COLUMNS'] = array();
				foreach ($category['_COLUMNS'] as $ckey => $column){
					//如果不存在自定义名称，使用默认名称
					empty($column['text']) && $column['text'] = $GLOBALS['lang']['cms.category_edit.view.colum.td.'.strtolower($ckey)];
					
					//如果是内容，因是默认必须字段，显示名称+类型。
					if($ckey == 'CONTENT') $category['COLUMNS'][] = $column['text'].$GLOBALS['lang']['cms.category_edit.view.colum.td.content.type.'.$column['type']];
					elseif($column['show']) $category['COLUMNS'][] = $column['text'];
				}
				
				$category['COLUMNS'] = implode('；', $category['COLUMNS']);
				
				if($category['IMAGEWIDTH']) $category['IMAGEWIDTH'] .= 'px';
				else $category['IMAGEWIDTH'] = $setting['ImageWidth'].'px';
				
				if($category['IMAGEHEIGHT']) $category['IMAGEHEIGHT'] .= 'px';
				else $category['IMAGEHEIGHT'] = $setting['ImageHeight'].'px';
				
				$categories[] = $category;
			}
		}
		
		include_once view('/module/cms/view/category');
	}
	
	//添加
	public function _add(){
		global $_var;
		
		$_log = new _log();
		$_user_category = new _user_category();
		$_category = new _category();
		
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0){
			$parent = $_category->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['CATEGORYID'] : 0;
		}
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.displayorder']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.cname']."<br/>";
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.identity']."<br/>";
			
			if(empty($_var['msg'])){
				$exists_category = $_category->get_by_identity($_var['gp_txtIdentity']);
				if($exists_category) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.exists']."<br/>";
				else{
					$columns = '';
					foreach($_var['gp_txtColumn'] as $key => $val){
						if($key == 'CONTENT') $columns .= '#'.$key.'|'.$val.'|'.$_var['gp_sltColumn']['CONTENT'];
						else $columns .= '#'.$key.'|'.$val.($_var['gp_cbxColumn'][$key] ? '|1' : '|0');
					}
					
					$categoryid = $_category->insert(array(
					'PARENTID' => $_var['gp_parentid'],
					'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
					'IDENTITY' => utf8substr($_var['gp_txtIdentity'], 0, 50),
					'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 200),
					'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
					'ISAUDIT' => $_var['gp_rdoIsAudit'] + 0, 
					'URL' => utf8substr($_var['gp_txtUrl'], 0, 200),
					'EDITER' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'TYPE' => 'article', 
					'COLUMNS' => $columns, 
					'FILES' => utf8substr($_var['gp_txtFiles'], 0, 100).($_var['gp_cbxColumn']['FILES'] ? '|1' : '|0'), 
					'FILE01' => $_var['gp_hdnFile01'], 
					'FILE02' => $_var['gp_hdnFile02']
					));
					
					$_user_category->insert(array(
					'USERID' => $_var['current']['USERID'], 
					'CATEGORYID' => $categoryid
					));
					
					$_category->update($categoryid, array(
					'PATH' => ($parent ? $parent['PATH'].','.$categoryid.',' : ','.$categoryid.',')
					));
					
					if($parent){
						$parent['CHILDREN'] = $parent['CHILDREN'] + 1;
						$_category->update($parent['CATEGORYID'], array('CHILDREN' => $parent['CHILDREN'] + 1));
					}
					
					$_log->insert($GLOBALS['lang']['cms.category.log.add']."({$_var[gp_txtCName]})", $GLOBALS['lang']['cms.category']);
					
					cache_delete('category_article');
					show_message($GLOBALS['lang']['cms.category.message.add'], "{ADMIN_SCRIPT}/cms/category&parentid={$_var[gp_parentid]}");
				}
			}
		}
		
		
		include_once view('/module/cms/view/category_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_category = new _category();
		
		$_var['gp_parentid'] = $_var['gp_parentid'] + 0;
		if($_var['gp_parentid'] > 0) {
			$parent = $_category->get_by_id($_var['gp_parentid']);
			$_var['gp_parentid'] = $parent ? $parent['CATEGORYID'] : 0;
		}
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/cms/category");
		
		$category = $_category->get_by_id($id);
		if($category == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/cms/category");
		
		$category = format_row_files($category);
		$category = $_category->format($category);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtDisplayOrder'])) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.displayorder']."<br/>";
			if(empty($_var['gp_txtCName'])) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.cname']."<br/>";
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.identity']."<br/>";
			
			if(empty($_var['msg'])){
				$exists_category = $_category->get_by_identity($_var['gp_txtIdentity']);
				if($exists_category && $category['CATEGORYID'] != $exists_category['CATEGORYID']) $_var['msg'] .= $GLOBALS['lang']['cms.category.validate.exists']."<br/>";
				else{
					$columns = '';
					foreach($_var['gp_txtColumn'] as $key => $val){
						if($key == 'CONTENT') $columns .= '#'.$key.'|'.$val.'|'.$_var['gp_sltColumn']['CONTENT'];
						else $columns .= '#'.$key.'|'.$val.($_var['gp_cbxColumn'][$key] ? '|1' : '|0');
					}
					
					$_category->update($category['CATEGORYID'], array(
					'CNAME' => utf8substr($_var['gp_txtCName'], 0, 30),
					'IDENTITY' => utf8substr($_var['gp_txtIdentity'], 0, 50),
					'COMMENT' => utf8substr($_var['gp_txtComment'], 0, 200),
					'DISPLAYORDER' => $_var['gp_txtDisplayOrder'] + 0,
					'ISAUDIT' => $_var['gp_rdoIsAudit'] + 0, 
					'URL' => utf8substr($_var['gp_txtUrl'], 0, 200),
					'EDITER' => $_var['current']['USERNAME'],
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'COLUMNS' => $columns, 
					'FILES' => utf8substr($_var['gp_txtFiles'], 0, 100).($_var['gp_cbxColumn']['FILES'] ? '|1' : '|0'), 
					'FILE01' => $_var['gp_hdnFile01'].'', 
					'FILE02' => $_var['gp_hdnFile02'].''
					));
					
					$_log->insert($GLOBALS['lang']['cms.category.log.update']."({$_var[gp_txtCName]})", $GLOBALS['lang']['cms.category']);
					
					cache_delete('category_article');
					
					show_message($GLOBALS['lang']['cms.category.message.update'], "{ADMIN_SCRIPT}/cms/category&parentid={$_var[gp_parentid]}");
				}
			}
		}
		
		include_once view('/module/cms/view/category_edit');
	}
	
	//上传图片
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
		
		$file_limit = 2;
		$file_uploaded = $_var['gp_uploaded'] + 0;
		
		if($file_limit > 0 && $file_limit < $file_uploaded + 1) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.limit']."{$file_limit}".$GLOBALS['lang']['admin.validate.swfupload.echo.limit.pic']);
			
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$upload->init($_FILES['Filedata'], 'cms');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach) {
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|0|'.$temp_img_size[0].'|'.$temp_img_size[1].'|'.$_var['gp_file']);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
}
?>