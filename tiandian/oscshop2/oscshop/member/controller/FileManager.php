<?php
/**
 *
 * @author    李梓钿
 *
 * 2016-06-24
 * 
 * 多用户图片管理器(只显示自己目录下的图片)
 * 
 * 图片只能一张张上传
 */
namespace osc\member\controller;
use osc\common\controller\ImageManager;
class FileManager extends ImageManager{
	

	protected function _initialize(){	
		
		define('UID',osc_service('member','user')->is_login());		

        if(!UID){  
			return $this->error('请先登录');
        }
		
		$this->init('mosc'.UID);
		
	}
	
}
?>