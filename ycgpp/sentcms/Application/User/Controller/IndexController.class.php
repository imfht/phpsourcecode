<?php
namespace User\Controller;

class IndexController extends \Common\Controller\UserController {

	public function index(){
		$member = D('Member');
		$uid = $this->mid;

		$data = $member->find($uid);
		$this->assign($data);
        $this->setSeo('用户中心');
		$this->display();
	}
}