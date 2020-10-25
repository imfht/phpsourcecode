<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;

class _AdminController extends Controller {
	protected function _initialize() {
		$this->CheckLogin();
		Hook::exec("Common\\Behavior\\AuthorizationBehavior", "Behavior", $this);
	}
	private function CheckLogin() {
		//检查登陆状态
		if (!isset($_SESSION['auth_group'])) {
			$this->error("请先登录", U("/Home/Index/Login"));
		}
	}
}
?>