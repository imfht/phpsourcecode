<?php

namespace Muushop\Controller;

use Think\Controller;
use Common\Controller\CommonController;

class BaseController extends CommonController {

	function _initialize()
	{
		parent::_initialize();
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
	 * 获取自定义导航
	 * @return [type] [description]
	 */
	private function custom_nav(){

		$custom_nav = S('custom_nav');
		if($custom_nav===false || $custom_nav=='' || empty($custom_nav)){
			$custom_nav = D('MuushopNav')->cache('custom_nav')->order('sort asc,id asc')->select();
			foreach($custom_nav as &$v){
				if(is_numeric($v['url']) || preg_match("/^\d*$/",$v['url'])){
					$v['url'] = U('Muushop/index/cats',array('id'=>$v['url']));
				}
				$child = D('MuushopNav')->where(array('pid' => $v['id']))->order('sort asc,id asc')->select();
				if($child){
					foreach($child as &$ch_v){
						if(is_numeric($ch_v['url']) || preg_match("/^\d*$/",$v['url'])){
							$ch_v['url'] = U('Muushop/index/cats',array('id'=>$ch_v['url']));
						}
					}
					unset($ch_v);
				}
			}
			unset($v);
			S('custom_nav',$custom_nav,3600);
		}
		return $custom_nav;
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
}