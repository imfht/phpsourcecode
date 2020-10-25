<?php
//版权所有(C) 2014 www.ilinei.com

namespace ad\control;

use ad\model\_ad;
use admin\model\_log;
use admin\model\_table;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/ad/lang.php';

/**
 * 广告位
 * @author sigmazel
 * @since v1.0.2
 */
class index{
	//默认
	public function index(){
		global $_var;
		
		$_ad = new _ad();
		$_log = new _log();
		$_table = new _table();
		
		$ad_types = array(
		'0' => $GLOBALS['lang']['ad.index_edit.view.type.select.0'], 
		'1' => $GLOBALS['lang']['ad.index_edit.view.type.select.1'], 
		'2' => $GLOBALS['lang']['ad.index_edit.view.type.select.2'], 
		'3' => $GLOBALS['lang']['ad.index_edit.view.type.select.3'], 
		'4' => $GLOBALS['lang']['ad.index_edit.view.type.select.4']
		);
		
		$table = $_table->get_by_identity('ad');
		
		$search = $_ad->search();
		
		if($_var['gp_do'] == 'delete'){
			$ad = $_ad->get_by_id($_var['gp_id']);
			if($ad){
				$_ad->delete($ad['ADID']);
				
				$_log->insert($GLOBALS['lang']['ad.index.log.delete']."({$ad[TITLE]})", $GLOBALS['lang']['ad.index']);
				
				file_clear($ad, $table['FILENUM']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$ad_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$ad = $_ad->get_by_id($val);
				if($ad){
					$_ad->delete($ad['ADID']);
					
					file_clear($ad, $table['FILENUM']);
					
					$ad_titles .= $ad['TITLE'].'， ';
				}
				
				unset($ad);
			}
			
			if($ad_titles) $_log->insert($GLOBALS['lang']['ad.index.log.delete.list']."({$ad_titles})", $GLOBALS['lang']['ad.index']);
		}
		
		$ads = array();
		$count = $_ad->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$ads = $_ad->get_list($start, $perpage, $search['wheresql']);
			foreach ($ads as $key => $ad){
				$ads[$key]['TYPENAME'] = $ad_types[$ad['TYPE']];
			}
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/ad", $perpage);
		}
		
		include_once view('/module/ad/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_ad = new _ad();
		$_log = new _log();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['ad.index_edit.validate.name']."<br/>";
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['ad.index_edit.validate.identity']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 50);
				
				$_ad->insert(array(
				'TITLE' => $_var['gp_txtTitle'],
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'TYPE' => $_var['gp_sltType'] + 0,
				'WIDTH' => $_var['gp_txtWidth'] + 0,
				'HEIGHT' => $_var['gp_txtHeight'] + 0, 
				'HASCATEGORY' => $_var['gp_cbxHasCategory'] + 0, 
				'CATEGORY_TYPE'=> $_var['gp_rdoCategoryType'] + 0, 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'].''
				));
				
				$_log->insert($GLOBALS['lang']['ad.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['ad.index']);
				
				show_message($GLOBALS['lang']['ad.index.message.add'], "{ADMIN_SCRIPT}/ad");
			}
		}
		
		include_once view('/module/ad/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_ad = new _ad();
		$_log = new _log();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/ad");
		
		$ad = $_ad->get_by_id($id);
		if($ad == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/ad"); 
		
		$ad = format_row_files($ad);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['ad.index_edit.validate.name']."<br/>";
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['ad.index_edit.validate.identity']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 50);
				
				$_ad->update($ad['ADID'], array(
				'TITLE' => $_var['gp_txtTitle'],
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'TYPE' => $_var['gp_sltType'] + 0,
				'WIDTH' => $_var['gp_txtWidth'] + 0,
				'HEIGHT' => $_var['gp_txtHeight'] + 0, 
				'HASCATEGORY' => $_var['gp_cbxHasCategory'] + 0, 
				'CATEGORY_TYPE'=> $_var['gp_rdoCategoryType'] + 0, 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'].''
				));
				
				$_log->insert($GLOBALS['lang']['ad.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['ad.index']);
				
				show_message($GLOBALS['lang']['ad.index.message.update'], "{ADMIN_SCRIPT}/ad&page={$_var[page]}&psize={$_var[psize]}");
			}
		}
		
		include_once view('/module/ad/view/index_edit');
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
			$upload->init($_FILES['Filedata'], 'ad');
			
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