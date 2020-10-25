<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/14 0021
 * Time: 上午 8:44
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 网站设置控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Pkadmin_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> model('setting_model', 'setting');
	}

	/**
	 * 网站设置首页
	 */
	public function setting() {
		$data = $this -> data;
		if ($_POST) {
			$params['title'] = $this -> input -> post('title');
			$params['sitename'] = $this -> input -> post('sitename');
			$params['keywords'] = $this -> input -> post('keywords');
			$params['footer'] = $this -> input -> post('footer');
			$params['description'] = $this -> input -> post('description');
			$this -> setting -> update_site_setting($params);
			$this -> pk -> add_log('修改网站配置信息！', $this -> ADMINISTRSTORS['admin_id'], $this -> ADMINISTRSTORS['username']);
			$success['msg'] = "网站信息设置成功！";
			$success['url'] = site_url("Pkadmin/Setting/setting");
			$success['wait'] = 3;
			$data['success'] = $success;
			$this -> load -> view('success.html', $data);
		} else {
			$this -> load -> view('setting.html', $data);
		}
	}

	/**
	 * 开发日志功能模块
	 */
	public function devlog() {
		$data = $this -> data;
		$data['devlog'] = $this -> setting -> get_devlog();
		$this -> load -> view('devlog.html', $data);
	}

}
