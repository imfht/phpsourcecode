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
 
namespace osc\mobile\controller;
use osc\admin\controller\Settings;
use think\Db;
class SettingsBackend extends Settings{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','移动端');	
	}
	
	function mobile(){
		$this->assign('breadcrumb2','配置管理');
		$this->assign('mobile',$this->get_config_by_module('mobile'));
		
		$this->assign('transport',Db::name('transport')->select());
		
		return $this->fetch();
	}

}
?>