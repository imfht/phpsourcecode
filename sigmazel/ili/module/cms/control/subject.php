<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\control;

use admin\model\_log;
use admin\model\_table;
use cms\model\_subject;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/cms/lang.php';

//专题 
class subject{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_subject = new _subject();
		
		$table = $_table->get_by_identity('subject');
		$search = $_subject->search();
	
		if($_var['gp_do'] == 'delete'){
			$subject = $_subject->get_by_id($_var['gp_id'] + 0);
			if($subject){
				$_subject->delete($subject['SUBJECTID']);
				$_log->insert($GLOBALS['lang']['cms.subject.log.delete']."({$subject[TITLE]})", $GLOBALS['lang']['cms.subject']);
				
				file_clear($subject, $table['FILENUM']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$subject_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$subject = $_subject->get_by_id($val);
				if($subject){
					$_subject->delete($subject['SUBJECTID']);
					
					file_clear($subject, $table['FILENUM']);
					
					$subject_titles .= $subject['TITLE'].'， ';
				}
			}
			
			if($subject_titles) $_log->insert($GLOBALS['lang']['cms.subject.log.delete.list']."({$subject_titles})", $GLOBALS['lang']['cms.subject']);
		}
		
		if($_var['gp_do'] == 'pass_list' && is_array($_var['gp_cbxItem'])){
			$subject_titles = '';
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_subject->get_by_id($val);
				if($tempdata){
					$_subject->update($tempdata['SUBJECTID'], array('ISAUDIT' => 1));
					
					$subject_titles .= $tempdata['TITLE'].'， ';
				}
				
				unset($tempdata);
			}
			
			if($subject_titles) $_log->insert($GLOBALS['lang']['cms.subject.log.pass.list']."({$subject_titles})", $GLOBALS['lang']['cms.subject']);
		}
		
		if($_var['gp_do'] == 'fail_list' && is_array($_var['gp_cbxItem'])){
			$subject_titles = '';
			foreach($_var['gp_cbxItem'] as $key => $val){
				$tempdata = $_subject->get_by_id($val);
				if($tempdata){
					$_subject->update($tempdata['SUBJECTID'], array('ISAUDIT' => 0));
					
					$subject_titles .= $tempdata['TITLE'].'， ';
				}
				
				unset($tempdata);
			}
			
			if($subject_titles) $_log->insert($GLOBALS['lang']['cms.subject.log.fail.list']."({$subject_titles})", $GLOBALS['lang']['cms.subject']);
		}
		
		$subjects = array();
		$count = $_subject->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$subjects = $_subject->get_list($start, $perpage, $search['wheresql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/cms/subject{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/cms/view/subject');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_subject = new _subject();
		
		$table = $_table->get_by_identity('subject');
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['cms.subject_edit.validate.identity']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['cms.subject_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtExpried']) && !is_datetime($_var['gp_txtExpried'])) $_var['msg'] .= $GLOBALS['lang']['cms.subject_edit.validate.expried']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 100);
				$_var['gp_txtSummary'] = utf8substr($_var['gp_txtSummary'], 0, 200);
				$_var['gp_txtAddress'] = utf8substr($_var['gp_txtAddress'], 0, 100);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 50);
				
				$_var['gp_txtSubTitle1'] = utf8substr($_var['gp_txtSubTitle1'], 0, 50);
				$_var['gp_txtSubTitle2'] = utf8substr($_var['gp_txtSubTitle2'], 0, 50);
				$_var['gp_txtSubTitle3'] = utf8substr($_var['gp_txtSubTitle3'], 0, 50);
				
				$subject_file_arr = file_upload_images($table['FILENUM']);
				
				$_subject->insert(array_merge(array(
				'TITLE' => $_var['gp_txtTitle'], 
				'SUBTITLE' => $_var['gp_txtSubTitle1'].'|'.$_var['gp_txtSubTitle2'].'|'.$_var['gp_txtSubTitle3'],
				'SUMMARY' => $_var['gp_txtSummary'],
				'ADDRESS' => $_var['gp_txtAddress'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'CONTENT' => $_var['gp_txtContent'],
				'ISAUDIT' => 0, 
				'ISTOP' => $_var['gp_rdoIsTop'] + 0,
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'EXPRIED' => $_var['gp_txtExpried'] ? $_var['gp_txtExpried'] : null,
				'PUBDATE' => date('Y-m-d H:i:s'),
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				), $subject_file_arr));
				
				$_log->insert($GLOBALS['lang']['cms.subject_edit.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['cms.subject']);
				
				show_message($GLOBALS['lang']['cms.subject_edit.message.add'], "{ADMIN_SCRIPT}/cms/subject");
			}
		}
		
		include_once view('/module/cms/view/subject_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_table = new _table();
		$_subject = new _subject();
		
		$table = $_table->get_by_identity('subject');
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/subject");
		
		$subject = $_subject->get_by_id($id);
		if($subject == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/subject"); 
		
		$subject['EXPRIED'] = $subject['EXPRIED'] > 0 ? date('Y-m-d H:i', strtotime($subject['EXPRIED'])) : '';
		
		$subject = format_row_files($subject);
		$subject_files = $_subject->get_files($subject, $table['FILENUM']);
		
		$subtitles = explode('|', $subject['SUBTITLE']);
		$subject['SUBTITLE1'] = $subtitles[0];
		$subject['SUBTITLE2'] = $subtitles[1];
		$subject['SUBTITLE3'] = $subtitles[2];
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtIdentity'])) $_var['msg'] .= $GLOBALS['lang']['cms.subject_edit.validate.identity']."<br/>";
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['cms.subject_edit.validate.title']."<br/>";
			if(!empty($_var['gp_txtExpried']) && !is_datetime($_var['gp_txtExpried'])) $_var['msg'] .= $GLOBALS['lang']['cms.subject_edit.validate.expried']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 100);
				$_var['gp_txtSummary'] = utf8substr($_var['gp_txtSummary'], 0, 200);
				$_var['gp_txtAddress'] = utf8substr($_var['gp_txtAddress'], 0, 100);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 50);
				
				$_var['gp_txtSubTitle1'] = utf8substr($_var['gp_txtSubTitle1'], 0, 50);
				$_var['gp_txtSubTitle2'] = utf8substr($_var['gp_txtSubTitle2'], 0, 50);
				$_var['gp_txtSubTitle3'] = utf8substr($_var['gp_txtSubTitle3'], 0, 50);
				
				$subject_file_arr = file_upload_images($table['FILENUM']);
				
				$_subject->update($subject['SUBJECTID'], array_merge(array(
				'TITLE' => $_var['gp_txtTitle'], 
				'SUBTITLE' => $_var['gp_txtSubTitle1'].'|'.$_var['gp_txtSubTitle2'].'|'.$_var['gp_txtSubTitle3'],
				'SUMMARY' => $_var['gp_txtSummary'],
				'ADDRESS' => $_var['gp_txtAddress'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'CONTENT' => $_var['gp_txtContent'],
				'ISAUDIT' => 0,
				'ISTOP' => $_var['gp_rdoIsTop'] + 0,
				'ISCOMMEND' => $_var['gp_eleIsCommend'] + 0,
				'EXPRIED' => $_var['gp_txtExpried'] ? $_var['gp_txtExpried'] : null,
				'PUBDATE' => date('Y-m-d H:i:s'),
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				), $subject_file_arr));
				
				$_log->insert($GLOBALS['lang']['cms.subject_edit.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['cms.subject']);
				
				show_message($GLOBALS['lang']['cms.subject_edit.message.update'], "{ADMIN_SCRIPT}/cms/subject&page={$_var[page]}&psize={$_var[psize]}");
			}
		}
		
		include_once view('/module/cms/view/subject_edit');
	}
	
	//查看
	public function _view(){
		global $_var;
		
		$_table = new _table();
		$_subject = new _subject();
		
		$table = $_table->get_by_identity('subject');
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/subject");
		
		$subject = $_subject->get_by_id($id);
		if($subject == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/subject"); 
		
		$subject['EXPRIED'] = $subject['EXPRIED'] > 0 ? date('Y-m-d H:i', strtotime($subject['EXPRIED'])) : '';
		
		$subject = format_row_files($subject);
		$subject_files = $_subject->get_files($subject, $table['FILENUM']);
		
		$subtitles = explode('|', $subject['SUBTITLE']);
		$subject['SUBTITLE1'] = $subtitles[0];
		$subject['SUBTITLE2'] = $subtitles[1];
		$subject['SUBTITLE3'] = $subtitles[2];
		
		include_once view('/module/cms/view/subject_view');
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
			$cimage = new image();
			
			$upload->init($_FILES['Filedata'], 'cms');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach){
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				$thumb = thumb_image($cimage, $upload->attach['target']);
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$temp_img_size[0].'|'.$temp_img_size[1]);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
}
?>