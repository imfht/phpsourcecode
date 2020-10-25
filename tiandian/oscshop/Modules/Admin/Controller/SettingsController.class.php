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
class SettingsController extends CommonController{
	
	protected function _initialize(){
		parent::_initialize();
			$this->breadcrumb1='系统';
			
	}
	
	function other(){
		$this->breadcrumb2='其他选项';
		$this->other=$this->get_config_by_group('other');
		$this->display();
	}	
	
	function image(){
		$this->breadcrumb2='图片尺寸';
		$this->image=$this->get_config_by_group('image');
		$this->display();
	}
	
	function general(){
		$this->breadcrumb2='基本信息';
		$this->site=$this->get_config_by_group('site');
		$this->display();
	}
		
	function smtp_mail(){
		$this->breadcrumb2='邮件配置';
		$this->smtp=$this->get_config_by_group('smtp');
		
		$this->display();
	}

	function get_config_by_group($group){
		
		$list=M('config')->where(array('config_group'=>$group))->select();
		if(isset($list)){
			foreach ($list as $k => $v) {
				$config[$v['name']]=$v;
			}
		}
		return $config;
	}
	
	function save(){
		if(IS_POST){
			$config=I('post.');					
			
			if($config && is_array($config)){
				$c=M('Config');    
	            foreach ($config as $name => $value) {
	                $map = array('name' => $name);
					$c->where($map)->setField('value', $value);					
	            }
				
	        }
	        S('DB_CONFIG_DATA',null);
	        $this->success('保存成功！');
		}
	}
	

}
?>