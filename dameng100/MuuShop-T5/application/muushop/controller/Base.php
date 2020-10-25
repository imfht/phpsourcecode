<?php

namespace app\muushop\controller;

use think\Controller;
use think\Db;
use app\common\controller\Common;

class Base extends Common {

	public $view_path;

	function _initialize()
	{
		parent::_initialize();
		//动态设置模板路径
		$pc_tpl = modC('MUUSHOP_SHOW_PC_TEMPLATE', '', 'Muushop');
		$mobile_tpl = modC('MUUSHOP_SHOW_MOBILE_TEMPLATE', '', 'Muushop');
		if($pc_tpl == '') $pc_tpl = 'muushop';
		if($mobile_tpl == '') $mobile_tpl = 'muushop';
		if(request()->isMobile()){
			$this->view_path = $mobile_tpl;
		}else{
			$this->view_path = $pc_tpl;
		}


		//商城配置
		$shopConfig = array(
			'title'=>modC('MUUSHOP_SHOW_TITLE', '', 'Muushop'),
			'logo'=>modC('MUUSHOP_SHOW_LOGO', '', 'Muushop'),
			'desc'=>modC('MUUSHOP_SHOW_DESC', '', 'Muushop'),
		);
		//商城自定义用户菜单
		$custom_nav = $this->custom_nav();
		$this->assign('shopConfig',$shopConfig);
		$this->assign('custom_nav',$custom_nav);
	}

	/**
	 * 初始化用户、判断用户登录
	 * @return [type] [description]
	 */
	public function init_user(){
		if(_need_login()){
			return get_uid();
		}else{
			$this->error('需要登录');
		}
	}
	/**
	 * 获取自定义导航
	 * @return [type] [description]
	 */
	private function custom_nav(){

		$custom_nav = cache('muushop_custom_nav');
		if($custom_nav===false || $custom_nav=='' || empty($custom_nav)){
			$custom_nav = Db::name('MuushopNav')->order('sort asc,id asc')->select();
			foreach($custom_nav as &$v){
				if(is_numeric($v['url']) || preg_match("/^\d*$/",$v['url'])){
					$v['url'] = url('muushop/Index/cats',array('id'=>$v['url']));
				}
				$child = Db::name('MuushopNav')->where(['pid' => $v['id']])->order('sort asc,id asc')->select();
				if($child){
					foreach($child as &$ch_v){
						if(is_numeric($ch_v['url']) || preg_match("/^\d*$/",$v['url'])){
							$ch_v['url'] = url('muushop/Index/cats',array('id'=>$ch_v['url']));
						}
					}
					unset($ch_v);
				}
			}
			unset($v);
			cache('muushop_custom_nav',$custom_nav,3600);
		}
		return $custom_nav;
	}

	 /**
     * 解析和获取模板内容 用于输出
     * @param string    $template 模板文件名或者内容
     * @param array     $vars     模板输出变量
     * @param array     $replace 替换内容
     * @param array     $config     模板参数
     * @param bool      $renderContent     是否渲染内容
     * @return string
     * @throws Exception
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {	
        //替换的内容
        $replace = [
	    	'__ZUI__'       => 'https://cdn.bootcss.com/zui/1.8.1',  
	        '__COMMON__'    => '/static/common',
	        '__JS__'        => '/static/muushop/'.$this->view_path.'/js',
	        '__IMG__'       => '/static/muushop/'.$this->view_path.'/images',
	        '__CSS__'       => '/static/muushop/'.$this->view_path.'/css', 
	        '__LIB__'       => '/static/muushop/'.$this->view_path.'/lib',  
	    ];
	    if($template == '') {
	    	$template =  strtolower(request()->controller().'/'.request()->action());
	    }
	    $template = $this->view_path.'/'.$template;
	    echo $this->view->fetch($template, $vars, $replace, $config);
    }

}