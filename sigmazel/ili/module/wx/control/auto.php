<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\control;

use admin\model\_log;
use wx\model\_wx_setting;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/wx/lang.php';

//自动回复
class auto{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		$wx_setting = format_row_file($wx_setting, 'AUTOPIC');
		
		if(!$wx_setting['WX_OPEN']) show_message($GLOBALS['lang']['wx.auto.message.open'], 0);
		
		if($_var['gp_formsubmit']){
			$_var['gp_rdoAutoType'] = $_var['gp_rdoAutoType'] + 0;
	
			if($_var['gp_rdoAutoType'] == 1){
				if(empty($_var['gp_txtAutoText'])) $_var['msg'] .= $GLOBALS['lang']['wx.auto.validate.text']."<br/>";
			}elseif($_var['gp_rdoAutoType'] == 2){
				if(empty($_var['gp_txtAutoTitle'])) $_var['msg'] .= $GLOBALS['lang']['wx.auto.validate.title']."<br/>";
				if(empty($_var['gp_txtAutoUrl'])) $_var['msg'] .= $GLOBALS['lang']['wx.auto.validate.url']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$_var['gp_txtAutoDescription'] = utf8substr($_var['gp_txtAutoDescription'], 0, 200);
				
				$_wx_setting->set($wx_setting, 'AUTOTYPE', $_var['gp_rdoAutoType']);
				$_wx_setting->set($wx_setting, 'AUTOTEXT', $_var['gp_txtAutoText']);
				$_wx_setting->set($wx_setting, 'AUTOTITLE', $_var['gp_txtAutoTitle']);
				$_wx_setting->set($wx_setting, 'AUTOURL', $_var['gp_txtAutoUrl']);
				$_wx_setting->set($wx_setting, 'AUTODESCRIPTION', $_var['gp_txtAutoDescription']);
				$_wx_setting->set($wx_setting, 'AUTOPIC', $_var['gp_hdnAutoPic']);
			}
			
			$_log->insert($GLOBALS['lang']['wx.auto.log.setup'], $GLOBALS['lang']['wx.auto']);

            cache_delete('wx_setting');
			show_message($GLOBALS['lang']['wx.auto.message.setup'], "{ADMIN_SCRIPT}/wx/auto");
		}
		
		include_once view('/module/wx/view/auto');
	}
	
	//上传图片
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
	
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$cimage = new image();
			
			$upload->init($_FILES['Filedata'], 'portal');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach){
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				$thumb = thumb_image($cimage, $upload->attach['target'], array('ImageWidth' => 720, 'ImageHeight' => 420, 'ThumbType' => 1));
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$temp_img_size[0].'|'.$temp_img_size[1]);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
}
?>