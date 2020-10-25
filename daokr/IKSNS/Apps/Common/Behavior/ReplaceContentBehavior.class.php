<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikcms.cn>
// +----------------------------------------------------------------------
namespace Common\Behavior;
use Think\Behavior;

defined('THINK_PATH') or exit();

// 初始化替换模板信息
class ReplaceContentBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(defined('BIND_MODULE') && BIND_MODULE === 'Install') return;
         $content = $this->_replace($content);
    }
    private function _replace($content) {
    	$replace = array();
        //爱客专用后台admin 静态文件地址
        $replace['__ADMIN_STATIC__'] = __ROOT__.'/Apps/Admin/Static';    	
    	//网站地址 带 / 如：http://www.ikphp.com/
        $replace['__SITE_URL__'] = C('ik_site_url');
        
        //网站APP静态文件目录
        $replace['__APP_STATIC__'] = C('ik_site_url').'Apps/'.MODULE_NAME.'/Static';
        //网站APP应用风格路径
        $replace['__STATIC_CSS__'] = C('ik_site_url').'Apps/'.MODULE_NAME.'/Static/css';
        //网站APP应用风格图片路径
        $replace['__STATIC_IMG__'] = C('ik_site_url').'Apps/'.MODULE_NAME.'/Static/images';
        //网站APP应用风格图片路径
        $replace['__STATIC_JS__'] = C('ik_site_url').'Apps/'.MODULE_NAME.'/Static/js';  
         
        //网站基本风格
        $basecss = 'Public/Theme/'.C('ik_site_theme').'/base.css';
        //APP风格默认样式
        $appcss = 'Apps/'.MODULE_NAME.'/Static/css/style.css'; 

        //APP风格下的controll_css样式
        $app_controll_css = 'Apps/'.MODULE_NAME.'/Static/css/'.strtolower(CONTROLLER_NAME).'.css';  
        
        $sitecss = '';
        
        if(is_file($basecss)){
        	$sitecss = '<link rel="stylesheet" type="text/css" href="'.C('ik_site_url').$basecss.'" id="baseTheme" />';
        }
        if(cookie('ikTheme')){
        	$iktheme = C('ik_site_url').'Public/Theme/'.cookie('ikTheme').'/base.css';
        	$sitecss .= '<link rel="stylesheet" type="text/css" href="'.$iktheme.'" id="ikTheme" />';
        }else{
        	$sitecss .= '<link rel="stylesheet" type="text/css" href="" id="ikTheme" />';
        } 
        if(is_file($appcss)){
        	$sitecss .= '<link rel="stylesheet" type="text/css" href="'.C('ik_site_url').$appcss.'" id="appTheme" />';
        }
        if(is_file($app_controll_css)){
        	$sitecss .= '<link rel="stylesheet" type="text/css" href="'.C('ik_site_url').$app_controll_css.'" id="controllTheme" />';
        }
        
         //开始替换css
        $replace['__SITE_THEME_CSS__'] = $sitecss;
        //扩展js
        $appextendjs = 'Apps/'.MODULE_NAME.'/Static/js/extend.func.js';
        if(is_file($appextendjs)){
        	$replace['__EXTENDS_JS__'] = '<script src="'.C('ik_site_url').$appextendjs.'" type="text/javascript"></script>';
        }else{
        	$replace['__EXTENDS_JS__'] = '';
        }
        
        //APP下的MODULE_NAME 对应的js
        $appmodulejs = 'Apps/'.MODULE_NAME.'/Static/js/'.strtolower(CONTROLLER_NAME).'.js';
        if(is_file($appmodulejs)){
        	$replace['__EXTENDS_JS__'] .= '<script src="'.C('ik_site_url').$appmodulejs.'" type="text/javascript"></script>';
        }        
        
        $content = str_replace(array_keys($replace),array_values($replace),$content);
    	return $content;
    }
}