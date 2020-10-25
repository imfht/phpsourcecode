<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\common\controller;
use think\Controller;
use think\Db;
abstract class ModuleInstall extends controller{
	
	protected function _initialize() {		
		
		if (!is_file(APP_PATH.'database.php')) {
			header('Location:'.request()->domain().'/install');
			die();
		}				
				
		$config =   cache('db_config_data');
		
        if(!$config){        	
            $config =   load_config();					
            cache('db_config_data',$config);
        }
		
        config($config); 
		
		define('UID',osc_service('admin','user')->is_login());

        if(!UID){  
			$this->redirect('admin/Login/login');
        }
		
		if(session('user_auth.username')!=config('administrator')){
		 	$this->error('请使用超级管理员账号进行操作！！');
		}
		
	}
	
	
	//安装模块配置
	public function install_module_config($data) {
		
		foreach ($data as $k => $d) {
			
			$config['name']=$d['name'];
			$config['value']=$d['value'];
			$config['info']=$d['info'];
			$config['module']=$d['module'];
			$config['module_name']=$d['module_name'];
			$config['extend_value']=$d['extend_value'];
			$config['use_for']=$d['use_for'];
			$config['status']=$d['status'];
			$config['sort_order']=$d['sort_order'];
			
            Db::name('config')->insert($config,false,true);
		}
		
	}
	
	//安装模块菜单
	public function install_module_menu($data,$pid) {
		
        if (empty($data) || !is_array($data)) {            
			return false;
        }        
       
        foreach ($data as $d) {
            
			$menu['module']=$d['module'];
			$menu['pid']=$pid;
			$menu['title']=$d['title'];
			$menu['url']=$d['url'];
			$menu['icon']=$d['icon'];
			$menu['sort_order']=$d['sort_order'];
			$menu['type']=$d['type'];
			$menu['status']=$d['status'];
			
            $newId = Db::name('menu')->insert($menu,false,true);
            //是否有子菜单
            if (!empty($d['children'])) {
                if ($this->install_module_menu($d['children'],$newId) !== true) {
                    return false;
                }
            }
        }
        return true;
    }
	
	//必须实现安装
    abstract public function install();

    //必须实现卸载
    abstract public function uninstall();
	
}
