<?php
namespace module\home;
use lib\Action,lib\RBAC;
class yijianMod extends Action{
    
	public function index() {
		$this->display();
	}
	public function baocun() {
		$db = model('yijian');
		$data = $db->checkData();
		
		$error = $db->getError();
		if($error){
			$this->error($error);
		}
		$db->insert();
		$this->success('感谢您的意见反馈');
	}
	
}