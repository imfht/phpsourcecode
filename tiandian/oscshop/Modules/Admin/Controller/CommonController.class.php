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
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	
     /* 初始化,权限控制,菜单显示 */
     protected function _initialize(){
        // 获取当前用户ID
        define('UID',is_login());
        if(!UID){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }
		/* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
     }
	 
	 
	/* 空操作，用于输出404页面 */
	public function _empty(){	
		// $this->display('Public:404');die();
		die('空操作');
	}
	
	/**
	 *跳转控制	 
	 */
	public function osc_alert($status){
				
		if($status['status']=='back'){
			$this->error($status['message']);
			die;					
		}elseif($status['status']=='success'){
			$this->success($status['message'],$status['jump']);
			die;
		}elseif($status['status']=='fail'){
			$this->error($status['message'],$status['jump']);
			die;
		}
	}
	 
}
?>