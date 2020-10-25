<?php
//版权所有(C) 2014 www.ilinei.com

namespace misc\control;

use admin\model\_setting;
use admin\model\_manager;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//上传图片
class order{
	//默认
	public function index(){
		global $_var, $db;

		$_setting = new _setting();
        $_manager = new _manager();

		//站内用户凭证无法保存时
		if($_var['gp__SALT'] && $_var['current']['SALT'] != $_var['gp__SALT']){
			//打开数据库
			$db->connect();
			
			$tmparr = explode(',', $_var['gp__SALT']);
			
			if(count($tmparr) == 2 && is_cint($tmparr[0])){
				if($tmparr[0] == -1){
					$manager = $_setting->get('SALT');
					
					if($manager['SALT'] != "-1,{$tmparr[1]}") $manager = null;
				}else{
					$manager = $_manager->get_by_id($tmparr[0]);
					
					if($manager['ISMANAGER'] == 0 || $manager['SALT'] != $tmparr[1]) $manager = null;
				}
				
				if(!$manager) exit_echo($GLOBALS['lang']['error']);
			}
		}
		
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$cimage = new image();
			
			$upload->init($_FILES['Filedata'], 'portal');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach) {
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				$thumb = thumb_image($cimage, $upload->attach['target'], array('ImageWidth' => 100, 'ImageHeight' => 100, 'ThumbType' => 2));
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$temp_img_size[0].'|'.$temp_img_size[1].'|'.$_var['gp_id']);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
}
?>