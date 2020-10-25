<?php
//版权所有(C) 2014 www.ilinei.com

namespace ad\control;

use ad\model\_ad;
use ad\model\_ad_log;
use admin\model\_log;
use admin\model\_table;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/ad/lang.php';

/**
 * 广告记录
 * @author sigmazel
 * @since v1.0.2
 */
class log{
	//默认
	public function index(){
		global $_var;
		
		$_ad = new _ad();
		$_ad_log = new _ad_log();
		$_log = new _log();
		$_table = new _table();
		
		$table = $_table->get_by_identity('ad_log');
		
		$ad_list = $_ad->get_all();
		$search = $_ad_log->search();
		
		if(is_array($_var['gp_sortno'])){
			foreach($_var['gp_sortno'] as $key => $val){
				$_ad_log->update($key, array('SORTNO' => $_var['gp_sortno'][$key]));
			}
		}
		
		if($_var['gp_do'] == 'delete'){
			$adlog = $_ad_log->get_by_id($_var['gp_id']);
			if($adlog){
				$_ad_log->delete($adlog['AD_LOGID']);
				$_log->insert($GLOBALS['lang']['ad.log.log.delete']."({$adlog[TITLE]})", $GLOBALS['lang']['ad.log']);
				
				file_clear($adlog, $table['FILENUM']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$adlog_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$adlog = $_ad_log->get_by_id($val);
				if($adlog){
					$_ad_log->delete($adlog['AD_LOGID']);
					
					file_clear($adlog, $table['FILENUM']);
					
					$adlog_titles .= $adlog['TITLE'].'， ';
				}
				
				unset($adlog);
			}
			
			if($adlog_titles) $_log->insert($GLOBALS['lang']['ad.log.log.delete.list']."({$adlog_titles})", $GLOBALS['lang']['ad.log']);
		}
		
		$count = $_ad_log->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$adlogs = $_ad_log->get_list($start, $perpage, $search['wheresql'], $search['ordersql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/ad/log{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/ad/view/log');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_ad = new _ad();
		$_ad_log = new _ad_log();
		$_log = new _log();
		
		$ad_list = $_ad->get_all();
		$search = $_ad_log->search();
		
		$adlog['ADID'] = $_var['gp_sltAdId'] + 0;
	
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['ad.log_edit.validate.name']."<br/>";
			if(empty($_var['gp_txtLink'])) $_var['msg'] .= $GLOBALS['lang']['ad.log_edit.validate.link']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtLink'] = utf8substr($_var['gp_txtLink'], 0, 200);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				
				$_ad_log->insert(array(
				'TITLE' => $_var['gp_txtTitle'],
				'LINK' => $_var['gp_txtLink'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'ADID' => $_var['gp_sltEAdId'] + 0,
				'SORTNO' => $_var['gp_txtSortNo'] + 0, 
				'BEGINDATE' => $_var['gp_txtAdBeginDate'], 
				'ENDDATE' => $_var['gp_txtAdEndDate'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'].'', 
				'FILE02' => $_var['gp_hdnFile02'].''
				));
				
				$_log->insert($GLOBALS['lang']['ad.log.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['ad.log']);
				
				show_message($GLOBALS['lang']['ad.log.message.add'], "{ADMIN_SCRIPT}/ad/log&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/ad/view/log_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_ad = new _ad();
		$_ad_log = new _ad_log();
		$_log = new _log();
		
		$ad_list = $_ad->get_all();
		$search = $_ad_log->search();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message('请正确操作！', "{ADMIN_SCRIPT}/ad/log");
		
		$adlog = $_ad_log->get_by_id($id);
		if($adlog == null) show_message('请正确操作！', "{ADMIN_SCRIPT}/ad/log"); 
		
		$adlog['BEGINDATE'] = $adlog['BEGINDATE'] > 0 ? date('Y-m-d', strtotime($adlog['BEGINDATE'])) : '';
		$adlog['ENDDATE'] = $adlog['ENDDATE'] > 0 ? date('Y-m-d', strtotime($adlog['ENDDATE'])) : '';
		
		$ad = $_ad->get_by_id($adlog['ADID']);
		$adlog['SIZE'] = "{$ad[WIDTH]}px * {$ad[HEIGHT]}px";
		
		$adlog = format_row_files($adlog);
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['ad.log_edit.validate.name']."<br/>";
			if(empty($_var['gp_txtLink'])) $_var['msg'] .= $GLOBALS['lang']['ad.log_edit.validate.link']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtLink'] = utf8substr($_var['gp_txtLink'], 0, 200);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 200);
				
				$_ad_log->update($adlog['AD_LOGID'], array(
				'TITLE' => $_var['gp_txtTitle'],
				'LINK' => $_var['gp_txtLink'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'ADID' => $_var['gp_sltEAdId'] + 0,
				'SORTNO' => $_var['gp_txtSortNo'] + 0, 
				'BEGINDATE' => $_var['gp_txtAdBeginDate'], 
				'ENDDATE' => $_var['gp_txtAdEndDate'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'FILE01' => $_var['gp_hdnFile01'].'', 
				'FILE02' => $_var['gp_hdnFile02'].''
				));
				
				$_log->insert($GLOBALS['lang']['ad.log.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['ad.log']);
				
				show_message($GLOBALS['lang']['ad.log.message.update'], "{ADMIN_SCRIPT}/ad/log&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/ad/view/log_edit');
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