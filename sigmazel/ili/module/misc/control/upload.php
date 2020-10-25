<?php
//版权所有(C) 2014 www.ilinei.com

namespace misc\control;

use admin\model\_setting;
use admin\model\_manager;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//文件上传
class upload{
	//默认
	public function index(){
		global $_var, $db, $setting;
		
		$_manager = new _manager();
		$_setting = new _setting();
		
		$json = array('error' => 0, 'message' => '', 'url' => '');
		
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
				
				if(!$manager){
					$json['error'] = 1;
					$json['message'] = 'Access Denied！';
					
					exit_json($json);
				}
			}
		}
		
		if($_FILES['imgFile']['name']){
			$upload = new \ilinei\upload();
			
			$upload->init($_FILES['imgFile'], 'portal');
			
			if(!in_array($_var['gp_dir'], array('image', 'file', 'flash', 'media'))) {
				$json['error'] = 1;
				$json['message'] = $GLOBALS['lang']['admin.validate.swfupload.echo.type'];
				
				exit_json($json);
			}
			
			$upload->save();
			
			if($upload->error()) {
				$json['error'] = 1;
				$json['message'] = $GLOBALS['lang']['admin.validate.swfupload.echo.move'];
				
				exit_json($json);
			}
			
			$json['error'] = 0;
			
			$fileext = strtolower(get_file_ext($upload->attach['target']));
			
			if($upload->attach['isimage'] && $setting['ThumbWidth'] && $setting['ThumbHeight']){
				$options = array(
				'ImageWidth' => $setting['ThumbWidth'] + 0, 
				'ImageHeight' => $setting['ThumbHeight'] + 0 , 
				'ThumbType' => 1
				);
				
				$cimage = new image();
				$tempimgsize = getimagesize('attachment/'.$upload->attach['target']);
				$thumb = $tempimgsize[0] > $options['ImageWidth'] || $tempimgsize[1] > $options['ImageHeight'] ? thumb_image($cimage, $upload->attach['target'], $options) : 0;
			}
			
			if($thumb) $json['url'] = 'attachment/'.$upload->attach['target'].'.t.'.$fileext;
			else $json['url'] = 'attachment/'.$upload->attach['target'];
			
			exit_json($json);
		}
	}
}
?>