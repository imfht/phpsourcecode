<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦  2014-3-17 0:10 开发
 * @Email:810578553@qq.com
 */
namespace Common\Controller;

class UserBaseController extends FrontendController {
	
	public function _initialize() {
		parent::_initialize ();		
		//当前设置项
		$this->_curr_menu(ACTION_NAME);
	}
	protected function _curr_menu($menu = 'setbase') {
		$menu_list = $this->_get_menu ();
		$this->assign ( 'user_menu_list', $menu_list );
		$this->assign ( 'user_menu_curr', $menu );
	}
	private function _get_menu() {
		$menu = array ();
    	$menu = array(
    				'setbase' => array('text'=>'基本信息', 'url'=>U('home/user/setbase')),
    				'setface' => array('text'=>'会员头像', 'url'=>U('home/user/setface')),
    				'setdoname' => array('text'=>'个性域名', 'url'=>U('home/user/setdoname')),
    				'setcity' => array('text'=>'常居地', 'url'=>U('home/user/setcity')),
    				'setpassword' => array('text'=>'修改密码', 'url'=>U('home/user/setpassword')),
    				//'bind' => array('text'=>'第三方绑定', 'url'=>U('user/bind')),
    			);
		return $menu;
	}
    
}