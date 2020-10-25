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
namespace osc\install\controller;
use think\Controller;
class Index extends controller{

	function index(){
	
		if (is_file(APP_PATH.'database.php')) {              
              return $this->error('已经成功安装，请不要重复安装!','/');
        }
		
		return $this->fetch();
	}
	 //安装完成
    public function complete(){
        $step = session('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("Install/step{$step}");
        }
		
        session('step', null);
        session('error', null);
        
		clear_cache();
		
       	return $this->fetch();
    }
}
