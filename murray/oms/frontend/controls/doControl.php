<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 执行
*/

defined('INPOP') or exit('Access Denied');

class frontend_doControl extends Frontend{

	//登陆
	function loginAction(){
		if($_POST['dosubmit']){
			$email = $_POST['mail'];
			$password = $_POST['password'];
			$toUrl = $_POST['tourl'] ? $_POST['tourl'] : SELF_URL;
			$isLogin = $this->_user->loginByMail($email, $password);
			if($isLogin){
				header("location:".$toUrl);
				exit;
			}else{
				$this->_log->logThis($this->_user->errormsg);
				print_r($_POST);
				exit;
			}
		}
		$tourl = $_GET['url'];
		$this->view->tourl = $tourl;
		$settingInfo = $this->_setting->getList();
		$this->view->settingInfo = $settingInfo;
		$this->render();
	}

	//退出
	function logoutAction(){
		$this->_user->logout();
		header("location:".SELF_URL);
		exit;
	}

	//更新密码
	function updatepasswordAction(){
		$uid = $this->_user->info['uid'];
		if($_POST['dosubmit']){
			$userArray = $_POST['user'];
			$oldpassword = $userArray['oldpassword'];
			$newpassword = $userArray['newpassword'];
			$userid = $this->_user->editPasswordByUser($uid, $oldpassword, $newpassword);
			if($userid > 0){
				header("location:".SELF_URL."do/dashboard/");
				exit;
			}else{
				print_r($userid);
				print_r($_POST);
				exit;
			}
		}
		$userInfo = $this->_user->getInfoById($uid);
		$this->view->userInfo = $userInfo;
		$this->render();
	}

	//仪表盘
	function dashboardAction(){
		$nums = array();
		$roleidArray = array();
		$roleids = $this->_user->info['roleids'];
		$roleidArray = explode(",", $roleids);
		//$isadmin = 1;
		if(in_array(1, $roleidArray)) $isadmin = 1;
		//统计所有业务数量
		$nums['former'] = formerService::doCount();
		//统计组织数量
		$nums['organization'] = organizationService::doCount();
		//统计用户数量
		$nums['user'] = $this->_user->getCount();
		//统计各个业务项数量
		$prototypes = formerService::getPrototypeList("dashboard=1");
		foreach($prototypes as $prototype){
			$info = $prototype;
			$info['num'] = formerService::getCacheTableCount($prototype['prototypeid']);
			$nums['operation'][$prototype['prototypeid']] = $info;
		}
		$this->view->isadmin = $isadmin;
		$this->view->nums = $nums;
		$this->render();
	}

	//操作员日志
	function logsAction(){
		$lastWeek = time() - (7 * 24 * 60 * 60);
		$start = date('Y-m-d', $lastWeek);
		$end = date('Y-m-d');
		$logString = $this->_log->returnLogMysqlContent($start, $end, "", true, '');
		$this->view->logString = $logString;
		$this->render();	
	}

	//系统配置
	function settingAction(){
		if($_POST['dosubmit']){
			$settingArray = $_POST['setting'];
			$done = 1;
			foreach($settingArray as $key=>$setting){
				$updateValue = $setting;
				$updateKey = $key;
				$id = $this->_setting->setValue($updateKey, $updateValue);
				if(!$id) $done = 0;
			}
			if($done > 0){
				header("location:".SELF_URL."do/setting/");
				exit;
			}else{
				print_r($_POST);
				exit;
			}
		}
		$settingArray = $this->_setting->getList();
		$this->view->settingArray = $settingArray;
		$this->render();	
	}

}

?>