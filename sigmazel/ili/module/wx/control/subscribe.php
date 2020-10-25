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

//关注回复
class subscribe{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_wx_setting = new _wx_setting();
		
		$wx_setting = $_wx_setting->get();
		$wx_setting = format_row_file($wx_setting, 'SUBSCRIBEPIC');
		
		if(!$wx_setting['WX_OPEN']) show_message($GLOBALS['lang']['wx.subscribe.message.open'], 0);

		if($_var['gp_formsubmit']){
			$_var['gp_rdoSubscribeType'] = $_var['gp_rdoSubscribeType'] + 0;
			
			if($_var['gp_rdoSubscribeType'] == 1){
				if(empty($_var['gp_txtSubscribeText'])) $_var['msg'] .= $GLOBALS['lang']['wx.subscribe.validate.text']."<br/>";
			}elseif($_var['gp_rdoSubscribeType'] == 2){
				if(empty($_var['gp_txtSubscribeTitle'])) $_var['msg'] .= $GLOBALS['lang']['wx.subscribe.validate.title']."<br/>";
				if(empty($_var['gp_txtSubscribeUrl'])) $_var['msg'] .= $GLOBALS['lang']['wx.subscribe.validate.url']."<br/>";
			}
			
			if(empty($_var['msg'])){
				$_var['gp_txtSubscribeDescription'] = utf8substr($_var['gp_txtSubscribeDescription'], 0, 200);
				
				$_wx_setting->set($wx_setting, 'SUBSCRIBETYPE', $_var['gp_rdoSubscribeType']);
				$_wx_setting->set($wx_setting, 'SUBSCRIBETEXT', $_var['gp_txtSubscribeText']);
				$_wx_setting->set($wx_setting, 'SUBSCRIBETITLE', $_var['gp_txtSubscribeTitle']);
				$_wx_setting->set($wx_setting, 'SUBSCRIBEURL', $_var['gp_txtSubscribeUrl']);
				$_wx_setting->set($wx_setting, 'SUBSCRIBEDESCRIPTION', $_var['gp_txtSubscribeDescription']);
				$_wx_setting->set($wx_setting, 'SUBSCRIBEPIC', $_var['gp_hdnSubscribePic']);
			}
			
			$_log->insert($GLOBALS['lang']['wx.subscribe.log.setup'], $GLOBALS['lang']['wx.subscribe']);

            cache_delete('wx_setting');
			show_message($GLOBALS['lang']['wx.subscribe.message.setup'], "{ADMIN_SCRIPT}/wx/subscribe");
		}
		
		include_once view('/module/wx/view/subscribe');
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