<?php
namespace module\home;
use lib\Action,lib\RBAC;
class meetingMod extends Action{
    
	public function index() {
		$this->display();
	}
	public function save() {	    
		$db = model('meeting');
		$count = $db->count();
		if($count>=20) $this->error('抱歉您来晚了，报名人数已满！<br>请关随时注微信内容，随时掌握长川活动动态。');
		$data = $db->checkData();		
		$error = $db->getError();
		if($error){
			$this->error($error);
		}
		$db->insert();
		$this->success('恭喜你报名成功');
	}
	
}