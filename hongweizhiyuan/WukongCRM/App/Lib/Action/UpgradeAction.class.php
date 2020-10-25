<?php 
class UpgradeAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array()
		);
		B('Authenticate', $action);
	}
	
	private $upgrade_site = "http://upgrade.5kcrm.com/";
	
	public function index(){	
		$params = array('version'=>C('VERSION'), 'release'=>C('RELEASE'), 'app'=>U('upgrade/index','','','',true));
		$info = sendRequest($this->upgrade_site . 'index.php?m=index&a=checkVersion', $params);
		if ($info){
			$this->ajaxReturn($info);
		} else {
			$this->ajaxReturn(0, L('CHECK_THE_NEW_VERSION_IS_WRONG'), 0);
		}
	}
	
	public function authorize(){	
		import("@.ORG.Unwrap");
		if(!file_exists(CONF_PATH.'license.dat')){
			$this->ajaxReturn(0, L('THE_CURRENT_SYSTEM_IS_CERTIFIED_AS_FREE_USER_PLEASE_OBSERVE_THE_5KCRM_GOKU'),0);
		}
		$padl = new padl();
		$padl->init(false);
		$key_file = file_get_contents(CONF_PATH.'license.dat');
		$key_info = $padl->_unwrap_license($key_file);

		if($key_info){
			$data['server'] = $key_info['server'];
			$data['company'] = $key_info['company'];
			$data['address'] = $key_info['address'];
			$data['type'] = $key_info['type'];
			$data['time'] = date('Y年m月d日', $key_info['time']);
			if($data['server'] == $_SERVER["SERVER_NAME"]){
				$this->ajaxReturn($data, L('LICENSE_INFORMATION'),1);
			}else{
				$this->ajaxReturn(0, L('ILLEGAL_AUTHORIZATION'), 0);
			}
		}else{
			$this->ajaxReturn(0, L('ILLEGAL_AUTHORIZATION'),0);
		}
	}



}