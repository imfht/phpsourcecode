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
namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Settings extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','系统');
				
	}

	
	function general(){			
		$this->assign('breadcrumb2','基本配置');
		$this->assign('common',$this->get_config_by_module('common'));
		return $this->fetch();
	}
	
	function get_config_by_module($module){
		
		$list=Db::name('config')->where('module',$module)->select();
		if(isset($list)){
			foreach ($list as $k => $v) {
				$config[$v['name']]=$v;
			}
		}
		return $config;
	}
	
	function save(){
		
		if(request()->isPost()){
			
			$config=input('post.');			
			
			if($config && is_array($config)){
				$c=Db::name('Config');    
	            foreach ($config as $name => $value) {
	                $map = array('name' => $name);
					$c->where($map)->setField('value', $value);					
	            }
				
	        }
	        clear_cache();
			storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'更新系统基本配置');	
	      return ['success'=>'更新成功'];
		  
		}
	}
	
	function other(){
		
		$this->assign('length',Db::name('length_class')->select());
		$this->assign('weight',Db::name('weight_class')->select());		
		$this->assign('order_status',Db::name('order_status')->select());		
		$this->assign('member_auth_group',Db::name('member_auth_group')->field('id,title')->select());		
		$this->assign('breadcrumb2','其他配置');
		
		return $this->fetch();
	}
	


}
?>