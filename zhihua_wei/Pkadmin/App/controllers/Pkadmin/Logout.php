<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/08 0035
 * Time: 下午 5:19
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 后台退出控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * 退出登录操作
	 */
	public function index() {
		$data['setting'] = $this -> pk -> get_setting();
		delete_cookie('auth');
		$success['msg'] = "退出成功，跳转到登录页！";
		$success['url'] = site_url("Pkadmin/Login/index");
		$success['wait'] = 3;
		$data['success'] = $success;
		$this -> load -> view('Pkadmin/success.html', $data);
	}

}
