<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class PublicController extends AdminbaseController {

	function _initialize() {
		
	}

	//后台登陆界面
	public function login() {
		$this->display();
	}

	public function logout() {
		unset($_SESSION['ADMIN_ID']);
		$this->redirect("public/login");
	}

	public function dologin() {
		$name = $_POST['username'];
		$pass = $_POST['password'];
		$verify = $_POST['verify'];
		//验证码
		if (!check_verify($verify)) {
			$this->error("验证码错误！");
		}
		$user = D("Users");
		$result = $user->where("user_login = '$name'")->find();
		if ($result != null) {
			if ($result['user_pass'] == md5($pass)) {
				//登入成功页面跳转
				session("user", $result);
				$result['last_login_ip'] = get_client_ip();
				$result['last_login_time'] = date("Y-m-d H:i:s");
				$user->save($result);
				$this->redirect('Index/index');
				$this->success("登录验证成功！", U("Index/index"));
			} else {
				$this->error("密码错误！");
			}
		} else {
			$this->error("用户名不存在！");
		}
	}

}
