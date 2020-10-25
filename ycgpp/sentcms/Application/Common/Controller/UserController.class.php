<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;

class UserController extends FrontController {

	public function _initialize(){
		parent::_initialize();
		$uid = intval($_REQUEST['uid']) ? intval($_REQUEST['uid']) : is_login();
		if (!$uid) {
			$this->redirect('User/Public/login' , 5);
		}
		$this->setLeftNav();
		$this->assign('uid', $uid);
		$this->assign('username', session('user_auth.username'));
		$this->mid = is_login();
	}

	protected function setLeftNav(){
		$left_nav = array(
			'config'   => array(
				array('name'=>'资料修改','url'=>U('User/Config/index'),'icon'=>''),
				array('name'=>'更换头像','url'=>U('User/Config/avatar'),'icon'=>''),
				array('name'=>'密码修改','url'=>U('User/Config/changepwd'),'icon'=>''),
			)
		);
		if ($this->getContentNav()) {
			$left_nav['content'] = $this->getContentNav();
		}

		Hook('extend_user_nav');
		$nav = C('extend_user_nav');
		foreach ($nav as $key => $value) {
			foreach ($value as $k => $v) {
				$left_nav[$k][] = $v;
			}
		}
		$this->assign('user_left_nav',$left_nav);
	}

	protected function getContentNav(){
		$list = D('Model')->where(array('extend'=>1))->select();
		if (empty($list)) {
			return false;
		}
		foreach ($list as $key => $value) {
			$data[] = array('name'=>$value['title'].'管理','url'=>U('User/'.$value['name'].'/index'));
		}
		return $data;
	}
}