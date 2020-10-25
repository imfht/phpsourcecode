<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 组件
*/

defined('INPOP') or exit('Access Denied');

class frontend_weigetControl extends Weiget{

	function headerAction(){
		$formerList = array();
		$roleidArray = array();
		$roleids = $this->_user->info['roleids'];
		$settingInfo = $this->_setting->getList();
		$roleidArray = explode(",", $roleids);
		$isadmin = 0;
		if(in_array(1, $roleidArray)) $isadmin = 1;
		$formerList = formerService::getPrototypeListByWorkflow($this->_user->info['uid']);
		$this->view->isadmin = $isadmin;
		$this->view->formerList = $formerList;
		$this->view->userInfo = $this->_user->info;
		$this->view->settingInfo = $settingInfo;
		$this->render();
	}

	function footerAction(){
		$settingInfo = $this->_setting->getList();
		$this->view->settingInfo = $settingInfo;
		$this->render();
	}

}

?>