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
class Base extends controller{
	
	protected function _initialize() {		
		
		if (!is_file(APP_PATH.'database.php')) {
			header('Location:'.request()->domain().'/install');
			die();
		}				
		
		$module=request()->module();
		
		if(!is_module_install($module)){
			die('该模块未安装');
		}
		
		$config =   cache('db_config_data');
		
        if(!$config){        	
            $config =   load_config();					
            cache('db_config_data',$config);
        }
		
        config($config); 
	}

}
