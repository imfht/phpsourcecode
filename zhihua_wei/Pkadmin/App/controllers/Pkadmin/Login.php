<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/02 0031
 * Time: 上午 9:19
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 后台登录控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> helper('captcha');
		$this -> load -> model('login_model', 'login');
	}

	/**
	 * 登录首页
	 */
	public function index() {
		$data['setting'] = $this -> pk -> get_setting();
		//判断是否已登录
		$flag = false;
		$auth = get_cookie('auth');
		if (!empty($auth)) {
			list($identifier, $token) = explode(',', $auth);
			if (ctype_alnum($identifier) && ctype_alnum($token)) {
				$admin = $this -> pk -> get_admin_info_by($identifier);
				if ($admin) {
					if ($token == $admin['token'] && $admin['identifier'] == password($admin['admin_id'] . md5($admin['username'] . $admin['salt']))) {
						$flag = true;
						$admin['head_pic'] = unserialize($admin['head_pic']);
						$this -> ADMINISTRSTORS = $admin;
					}
				}
			}
		}
		//如果已登录则直接跳转到首页
		if ($flag) {
			$success['msg'] = "您已经登录，正在跳转到主页！";
			$success['url'] = site_url("Pkadmin/Admin/index");
			$success['wait'] = 3;
			$data['success'] = $success;
			$this -> load -> view('Pkadmin/success.html', $data);
		} else {
			$this -> load -> view('Pkadmin/login.html', $data);
		}
	}

	/**
	 * 登录处理
	 */
	public function singin() {
		$data['setting'] = $this -> pk -> get_setting();
		$verify_code = strtolower($this -> input -> post('verify_code'));
		$code = strtolower($this -> session -> userdata('verify_code'));
		if ($verify_code === $code) {
			$username = trim($this -> input -> post('username'));
			$password = password(trim($this -> input -> post('password')));
			$remember = $this -> input -> post('remember');
			$admin = $this -> login -> get_admin_info($username, $password);
			if ($admin) {
				$token = password(uniqid(rand(), TRUE));
				$salt = random(10);
				$identifier = password($admin['admin_id'] . md5($admin['username'] . $salt));
				//将个人认证存入到cookie
				$auth = $identifier . ',' . $token;

				$this -> login -> set_admin_auth($admin['admin_id'], $identifier, $token, $salt);
				if ($remember) {
					set_cookie('auth', $auth, 3600 * 24 * 365);
				} else {
					set_cookie('auth', $auth, 3600);
				}
				$this -> pk -> add_log('登录成功！', $admin['admin_id'], $admin['username']);
				redirect('Pkadmin/Admin/index');
			} else {
				$error['msg'] = "用户名密码输入错误，请重新输入！";
				$error['url'] = site_url("Pkadmin/Login/index");
				$error['wait'] = 3;
				$data['error'] = $error;
				$this -> load -> view('Pkadmin/error.html', $data);
			}
		} else {
			$error['msg'] = "验证码错误，请重新登录！";
			$error['url'] = site_url("Pkadmin/Login/index");
			$error['wait'] = 3;
			$data['error'] = $error;
			$this -> load -> view('Pkadmin/error.html', $data);
		}
	}

	/**
	 * 生成验证码
	 */
	public function verify_code() {
		//调用函数生成验证码
		$vals = array('img_width' => '100', 'img_height' => '35', 'word_length' => 6);
		$verify_code = create_captcha($vals);
		$this -> session -> set_userdata('verify_code', $verify_code);
	}

	/**
	 * 访问错误页面
	 */
	public function visit_error() {
		$data['setting'] = $this -> pk -> get_setting();
		$error['msg'] = "您没有权限访问本页面";
		$error['url'] = site_url("Pkadmin/Login/index");
		$error['wait'] = 3;
		$data['error'] = $error;
		$this -> load -> view('Pkadmin/error.html', $data);
	}

}
