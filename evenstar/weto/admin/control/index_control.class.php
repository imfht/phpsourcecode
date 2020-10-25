<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class index_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
	}
	
	function on_login() {
		$user = $this->user->read($this->_user['uid']);
		
		$error = array();
		$referer = core::gpc('HTTP_REFERER', 'S');
		if(strpos($referer, 'index-logout') !== FALSE || strpos($referer, 'index-login') !== FALSE || strpos($referer, 'user-login') !== FALSE) {
			$referer = '';
		}
		if($this->form_submit()) {
			$referer = core::gpc('referer', 'P');
			$password = core::gpc('password', 'P');
			$clienttime = core::gpc('clienttime', 'P');
			$safe_admin_auth = core::gpc('safe_admin_auth', 'P');
			
			if(!$this->user->verify_password($password, $user['password'], $user['salt'])) {
				$error['password'] = '密码不对！';
			}
			
			misc::setcookie($this->conf['cookie_pre'].'safe_admin_auth', intval($safe_admin_auth), $clienttime + 86400 * 30, $this->conf['cookie_path'], $this->conf['cookie_domain']);
			
			// 登陆成功，设置 cookie
			$s = encrypt("$_SERVER[time]\t$_SERVER[ip]", $this->conf['auth_key']);
			
			// 客户端时间解决服务器与客户端不在一个时区的问题。
			misc::setcookie($this->conf['cookie_pre'].'admin_auth', $s, $clienttime + 3600, $this->conf['cookie_path'], $this->conf['cookie_domain']);
			//$this->message('登录后台成功！', 1, $referer ? $referer : './');
		}
		
		$this->view->assign('referer', $referer);
		$this->view->assign('error', $error);
		$this->view->display('index_login.htm');
	}
	
	function on_logout() {
		misc::setcookie($this->conf['cookie_pre'].'admin_auth', '', $_SERVER['time'], $this->conf['cookie_path'], $this->conf['cookie_domain']);
		$url = "?index-login.htm";
		echo '<html><body><script>top.location="'.$url.'"</script></body></html>';
		exit;
	}
	
	
	public function on_index() {
		$this->view->display('index_index.htm');
	}
	
	function on_top() {
		$this->view->display('index_top.htm');
	}
	
	function on_menu() {
		// 展开菜单
		$type = core::gpc('type');
		$this->view->assign('type', $type);
		$this->view->display('index_menu.htm');
	}
	
	function on_main() {
		$this->check_admin_group();
		$info = array();
		$info['disable_functions'] = ini_get('disable_functions');
		$info['allow_url_fopen'] = ini_get('allow_url_fopen') ? '是' : '否';
		$info['safe_mode'] = ini_get('safe_mode') ? '是' : '否';
		empty($info['disable_functions']) && $info['disable_functions'] = '无';
		$info['upload_max_filesize'] = ini_get('upload_max_filesize');
		$info['post_max_size'] = ini_get('post_max_size');
		$info['memory_limit'] = ini_get('memory_limit');
		$info['max_execution_time'] = ini_get('max_execution_time');
		$info['dbversion'] = $this->user->db->version();
		$info['SERVER_SOFTWARE'] = core::gpc('SERVER_SOFTWARE', 'S');
		$lastversion = $this->get_last_version();
		
		$stat = array();
		$stat['threads'] = $this->thread->count();
		$stat['posts'] = $this->post->count();
		$stat['users'] = $this->user->count();
		$stat['attachs'] = $this->attach->count();
		$stat['disk_free_space'] = function_exists('disk_free_space') ? misc::humansize(disk_free_space('./')) : '未知';
		
		
		// 潜在错误检测，目录可写检测，避免搬家导致的问题。
		$check = array();
		$upload_tmp_dir = ini_get('upload_tmp_dir');
		if(!empty($upload_tmp_dir)) {
			$check['upload_path_check'] = !misc::is_writable($upload_tmp_dir) ? "<span class\"red\">$upload_tmp_dir 不可写</span>，上传功能会受到影响。" : "<span class=\"green\">$upload_tmp_dir 可写</span>";
		} else {
			$check['upload_path_check'] = "<span class=\"red\">php.ini 中未设置 upload_tmp_dir，可能会导致上传失败 </span>";
		}
		
		$check['php_ini'] = ini_get('upload_tmp_dir');
		
		$this->view->assign('info', $info);
		$this->view->assign('stat', $stat);
		$this->view->assign('check', $check);
		$this->view->assign('lastversion', $lastversion);
		
		// hook admin_index_main_view_before.php
		
		$this->view->display('index_main.htm');
	}
	
	public function on_phpinfo() {
		$this->check_admin_group();
		unset($_SERVER['models']);
		phpinfo();
	}
	
	// 测试smtp是否正常
	public function on_testfsockopen() {
		$fp = fsocketopen('smtp.qq.com', 25, $errno, $errstr, 5);
		fclose($fp);
		if($errno) {
			$this->message('失败，错误代码：'.$errstr, 0);
		} else {
			$this->message('成功'.$errstr, 1);
		}
	}
	
        private function get_last_version() {
        	$last_version = $this->kv->get('last_version');
		if($_SERVER['time'] - $last_version > 86400) {
			$this->kv->set('last_version', $_SERVER['time']);
			$sitename = urlencode($this->conf['app_name']);
			$sitedomain = urlencode($this->conf['app_url']);
			$version = urlencode($this->conf['version']);
			$users = $this->user->count();
			$threads = $this->thread->count();
			$posts = $this->post->count();
			return '<'.'sc'.'ri'.'pt src="htt'.'p:'.'/'.'/c'.'ust'.'om'.'.xi'.'u'.'no.'.'co'.'m/version.htm'.'?'.'&sitename='.$sitename.'&sitedomain='.$sitedomain.'&users='.$users.'&threads='.$threads.'&posts='.$posts.'&version='.$version.'">'.'<'.'/s'.'cr'.'ip'.'t>';
		} else {
			return '';
		}
        }
        
        //hook admin_index_control_after.php

}

?>