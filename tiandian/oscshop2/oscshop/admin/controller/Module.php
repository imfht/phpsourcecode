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
class Module extends AdminBase{
	
    public function index()
    {    	
		$this->check_module();		
		$this->assign('list',Db::name('module')->select());		
    	$this->assign('breadcrumb1','扩展');
		$this->assign('breadcrumb2','模块管理');		    
		return $this->fetch();   
    }
	//检测并安装模块
	public function check_module(){
		
		$dirtool = new \oscshop\Dir(APP_PATH);   
		
		foreach ($dirtool->getIterator() as $k => $v) {
			if($v['type']=='dir'){
				if(!in_array($v['filename'],array('admin','common','install'))){
					 if (is_file(APP_PATH.$v['filename'].'/module.php')) {
                 
                        $rules = include APP_PATH.$v['filename'].'/module.php';
                        if (is_array($rules)) {
                          
                           $module[$v['filename']]=$rules;
                        }
                    }
				}
			}
			
		}
		$list=Db::name('module')->field('module,modulename,disabled,author')->select();
	
		foreach ($list as $k => $v) {
			$install[$v['module']]=$v['module'];
		}
		//不存在该模块就写入表中
		foreach ($module as $k => $v) {
			if(!isset($install[$k])){
				$m['module']=$v['module'];
				$m['modulename']=$v['modulename'];
				$m['base_module']=$v['base_module'];
				$m['version']=$v['version'];
				$m['author']=$v['author'];				
				$m['installtime']=date('Y-m-d',time());				
				$m['description']=$v['description'];
				Db::name('module')->insert($m);
			}
		}
		clear_cache();
	}

}
