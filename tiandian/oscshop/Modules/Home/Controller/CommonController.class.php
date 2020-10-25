<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller{
	
     /* 初始化,权限控制,菜单显示 */
     protected function _initialize(){
		/* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');		
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置        
        if(!C('WEB_SITE_CLOSE')){
           die('站点已经关闭，请稍后访问~');
        }
		 visitors_ip();
     }
	 
	 
	/* 空操作，用于输出404页面 */
	public function _empty(){	
		// $this->display('Public:404');die();
		die('空操作');
	}
	

	 
}
?>