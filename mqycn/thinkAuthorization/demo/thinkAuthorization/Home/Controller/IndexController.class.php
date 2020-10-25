<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
	public function index() {
		if (isset($_SESSION['auth_group'])) {
			$this->display();
		} else {
			$this->error("请先登陆", U("/Home/Index/Login"));
		}
	}

	public function Login() {
		$this->display("Login");
	}

	public function LoginError() {
		$this->display("LoginError");
	}

	public function LoginOut() {
		session_destroy();
		$this->Login();
	}

	public function CheckLogin() {
		$u = I("post.admin");
		if ($u == 'admin') {
			#管理员登陆
			$g = 1;
		} else {
			#普通会员
			$g = 2;
			$u = "普通用户";
		}
		$_SESSION['user'] = $u;
		$_SESSION['auth_group'] = $g;

		$AuthGroup = M("AuthGroup");
		$auth = $AuthGroup->find($g);
		unset($AuthGroup);
		if ($auth) {
			$this->success("登陆成功", U($auth['ag_login']));
		} else {
			$this->error("系统异常，请重新导入数据库");
		}
	}

}