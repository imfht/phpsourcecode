<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class admin_control extends common_control {
	
	function __construct(&$conf) {
		// hook admin_control_construct_before.php
		parent::__construct($conf);
		// hook admin_control_construct_after.php
		global $bbsconf;
		$this->view->assign('bbsconf', $bbsconf);
		
		// 设置风格不受前台影响
		$this->conf['first_view_path'] = array(BBS_PATH.'admin/view/', BBS_PATH.'view/');
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		// 检查来路是否合法
		if(!defined('XIUNO_SKIP_CHECK_XSS')) {
			$this->check_xss();
		}
		// 这里可能会有跨站脚本导致的提交，可以触发安全警报。管理员应该定期查看后台日志。
		$this->check_mod_group();
		$admin_auth = core::gpc($this->conf['cookie_pre'].'admin_auth', 'R');
		$this->view->assign('_admin_auth', $admin_auth);
		
		$login = 0;
		if(empty($admin_auth)) {
			// 登录页面放行
			 if(!(core::gpc(0) == 'index' && (core::gpc(1) == 'login' || core::gpc(1) == 'logout'))) {
				$login = 1;
			}
		} else {
			$s = decrypt($admin_auth, $this->conf['auth_key']);
			
			if(empty($s)) {
				$login = 1;
			} else {
				$arr = explode("\t", $s);
				if(empty($arr)) {
					$login = 1;
				} else {
					list($time, $ip) = $arr;
					
					/*
						1. cookie 连续实效半小时，半小时不活动视为自动退出。
						2. IP发生变化，退出登录
						3. 更换浏览器，退出登录（浏览器同样可以被xss获取，此项废除）
					*/
					if($_SERVER['time'] - $time > 1800) {
						$s = encrypt("$_SERVER[time]\t$_SERVER[ip]", $this->conf['auth_key']);
						misc::setcookie($this->conf['cookie_pre'].'admin_auth', $s, $_SERVER['time'] + 3600, $this->conf['cookie_path'], $this->conf['cookie_domain']);
						$time = $_SERVER['time'];
					}
					/*
					if(core::gpc(0) == 'forum' && core::gpc(1) == 'uploadicon') {
						$login = 0;
					}
					*/
					
					// 仅比较IP段！放宽一点。
					$pos = strrpos($ip, '.');
					if(substr($ip, 0, $pos) != substr($_SERVER['ip'], 0, $pos)) {
						$login = 1;
					}
				}
			}
			// dateline	ip
		}
		if($login) {
			misc::setcookie($this->conf['cookie_pre'].'admin_auth', '', $_SERVER['time'], $this->conf['cookie_path'], $this->conf['cookie_domain']);
			$url = "?index-login.htm";
			echo '<html><body><script>top.location="'.$url.'"</script></body></html>';
			exit;
		}
		// hook admin_control_check_after.php
	}
	
	// 检查 xss
	protected function check_xss() {
		global $bbsconf;
		$referer = core::gpc('HTTP_REFERER', 'S');
		// 如果来自前台，则停顿一下！点击后继续访问
		$len = strlen($bbsconf['app_url']);
		$len2 = strlen($this->conf['app_url']);
		$url = misc::get_script_uri();
		if(substr($referer, 0, $len2) == substr($this->conf['app_url'], 0, $len2)) {
			// 合法
		} elseif(empty($referer) || substr($referer, 0, $len) == substr($bbsconf['app_url'], 0, $len)) {
			$this->message("<a href=\"$url\">通过了来路检查, 点击【继续访问管理后台】</a>");
		} else {
			$this->message("此来路可能存在安全问题：<br />$referer, <br /><br /><a href=\"$url\">点击【继续访问管理后台】</a>");
		}
	}
	
	// 是否为最高级别的管理员
	protected function check_admin_group() {
		if($this->_group['groupid'] != 1) {
			$this->message('对不起，您不是管理员，无权访问。', 0, '?index-login.htm');
		}
	}
	
	protected function check_mod_group() {
		if($this->_group['groupid'] == 0 || $this->_group['groupid']  > 5) {
			log::write("非法尝试后台登录", 'login.php');
			$this->message('对不起，您所在的用户组不是管理组，无权访问。', 0, $this->_group['groupid'] == 0 ? '../?user-login.htm' : '?index-login.htm');
		}
	}
	
	//hook admin_admin_control_after.php
}

?>