<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class user_control extends common_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		
		// resetpw_on, reg_email_on, resetpw_on, reg_email_on
		$this->conf += $this->kv->xget('conf_ext');
		
	}
	
	// ajax 登录
	public function on_login() {
		// hook user_login_start.php
		if(!$this->form_submit()) {
			
			// hook user_login_before.php
			if(core::gpc('ajax')) {
				$this->view->display('user_login_ajax.htm');
			} else {
				$referer = $this->get_referer();
				$this->view->assign('referer', $referer);
				$this->view->display('user_login.htm');
			}
		} else {
			$userdb = $error = array();
			$email = core::gpc('email', 'P');
			$password = core::gpc('password', 'P');
			$clienttime = core::gpc('clienttime', 'P');
			
			if(empty($email)) {
				$error['email'] = '请填写用户名或Email';
				$this->message($error);
			}
			
			// hook user_login_check_before.php
			$userdb = $this->user->get_user_by_email($email);
			if(empty($userdb)) {
				$userdb = $this->user->get_user_by_username($email);
				if(empty($userdb)) {
					$error['email'] = '用户名/Email 不存在';
					log::write('EMAIL不存在:'.$email, 'login.php');
					$this->message($error);
				}
			}
			$uid = $userdb['uid'];
			
			if(!$this->user->verify_password($password, $userdb['password'], $userdb['salt'])) {
				$error['password'] = '密码错误!';
				$log_password = '******'.substr($password, 6);
				log::write("密码错误：$email - $log_password", 'login.php');
				$this->message($error);
			}
			
			// hook user_login_check_after.php
			if(!array_filter($error)) {
				$error = array();
				$error['user']['username'] =  $userdb['username'];
				$error['user']['auth'] =  $this->user->get_xn_auth($userdb);
				$error['user']['groupid'] =  $userdb['groupid'];
				
				// hook user_login_succeed.php
				$this->user->set_login_cookie($userdb, $clienttime + 86400 * 30);
				
				// 更新在线列表
				$this->update_online();
			}
			$this->message($error);
			
		}
	}
	
	public function on_logout() {
		
		// hook user_logout_start.php
		$error = array();
		if(!$this->form_submit()) {
			
			// hook user_logout_before.php
			if(core::gpc('ajax')) {
				$this->view->display('user_logout_ajax.htm');
			} else {
				$referer = $this->get_referer();
				$this->view->assign('referer', $referer);
				$this->view->display('user_logout.htm');
			}
		} else {
			
			// 清除 online username
			$sid = $this->_sid;
			$online = $this->online->read($sid);
			if($online) {
				$online['groupid'] = 0;
				$online['uid'] = 0;
				$online['username'] = '';
				$this->online->update($online);
			}
			
			// hook user_logout_after.php
			misc::setcookie($this->conf['cookie_pre'].'auth', '', 0, $this->conf['cookie_path'], $this->conf['cookie_domain']);
			$this->message($error);
		}
	}
	
	// ajax 注册
	public function on_create() {
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		if(!$this->conf['reg_on']) {
			$this->message('当前注册功能已经关闭。', 0);
		}
		// hook user_create_start.php
		if(!$this->form_submit()) {
			
			// hook user_create_before.php
			if(core::gpc('ajax')) {
				$this->view->display('user_create_ajax.htm');
			} else {
				$referer = $this->get_referer();
				$this->view->assign('referer', $referer);
				$this->view->display('user_create.htm');
			}
		} else {
			
			// 接受数据
			$userdb = $error = array();
			$email = core::gpc('email', 'P');
			$username = core::gpc('username', 'P');
			$password= core::gpc('password', 'P');
			$password2 = core::gpc('password2', 'P');
			$clienttime = core::gpc('clienttime', 'P');
			
			// check 数据格式
			$error['email'] = $this->user->check_email($email);
			$error['email_exists'] = $this->user->check_email_exists($email);
			
			// 如果email存在
			if($error['email_exists']) {
				// 如果该Email一天内没激活，则删除掉，防止被坏蛋“占坑”。
				$uid = $this->user->get_uid_by_email($email);
				$_user = $this->user->read($uid);
				if($_user['groupid'] == 6 && $_SERVER['time'] - $_user['regdate'] > 86400) {
					$this->user->delete($uid);
					$error['email_exists'] = '';
				}
			}
			$error['username'] = $this->user->check_username($username);
			$error['username_exists'] = $this->user->check_username_exists($username);
			$error['password'] = $this->user->check_password($password);
			$error['password2'] = $this->user->check_password2($password, $password2);
			
			$groupid = $this->conf['reg_email_on'] ? 6 : 11;
			$salt = substr(md5(rand(10000000, 99999999).$_SERVER['time']), 0, 8);
			$user = array(
				'username'=>$username,
				'email'=>$email,
				'password'=>$this->user->md5_md5($password, $salt),
				'groupid'=>$groupid,
				'salt'=>$salt,
			);
			
			// hook user_create_after.php
			
			// 判断结果
			if(!array_filter($error)) {
				$error = array();
				$uid = $this->user->xcreate($user);
				if($uid) {
					// 发送激活邮件
					if($this->conf['reg_email_on']) {
						try {
							$this->send_active_mail($uid, $username, $email, $error);	// $error['email_smtp_url']
						} catch(Exception $e) {
							$error['emailsend'] = '激活邮件发送失败！';
						}
					}
					
					// 此处由 $error 携带部分用户信息返回。
					$userdb = $this->user->read($uid);
					$error['user'] = array();
					$error['user']['username'] = $userdb['username'];
					$error['user']['auth'] = $this->user->get_xn_auth($userdb);
					$error['user']['groupid'] = $userdb['groupid'];
					$error['user']['uid'] = $uid; // 此处遗漏，感谢杨永全细心指正。
					
					$this->user->set_login_cookie($userdb, $clienttime + 86400 * 30);
					$this->runtime->xset('users', '+1');
					$this->runtime->xset('todayusers', '+1');
					$this->runtime->xset('newuid', $uid);
					$this->runtime->xset('newusername', $userdb['username']);
					// $this->runtime->xsave();
					
					// hook user_create_succeed.php
					
				}
			}
			$this->message($error);
		}
	}
	
	// 重新发送激活连接
	public function on_reactive() {
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		// 检查是否已经激活
		$user = $this->user->read($this->_user['uid']);
		if(empty($user)) {
			$this->message('用户不存在！');
		}
		if($user['groupid'] != 6) {
			$this->message('您的账户已经激活，无需再次获取激活码！');
		}
		
		// 判断上次激活的时间（这里重用注册时间）
		if($_SERVER['time'] - $user['regdate'] > 86400) {
			$error = array();
			try {
				
				// hook user_reactive_before.php
				$this->send_active_mail($user['uid'], $user['username'], $user['email'], $error);
				
				// 更新最后发送的时间，防止重复发送
				$user['regdate'] = $_SERVER['time'];
				$this->user->update($user);
			} catch(Exception $e) {
				
				log::write('发送激活码失败:'.$user['email'].', error:'.$e->getMessage(), 'login.php');
				
				$this->message('可能服务器繁忙，发送邮件失败，请您明天再来尝试获取激活码！');
			}
			
			if(empty($error)) {
				$this->message($error);
			} else {
				$emailarr = explode('@', $user['email']);
				$emailinfo = $this->mmisc->get_email_site($emailarr[1]);
				$url = "<a href=\"$emailinfo[url]\" target=\"_blank\"><b>【$emailinfo[name]】</b></a>";
				$this->message('已经重新给您的信箱 ('.$user['email'].') 发送了激活码邮件：登录：'.$url);
			}
			
		} else {
			$this->message('一天只能获取激活一次激活码！');
		}
	}
	
	// 重设密码，邮箱验证
	public function on_resetpw() {
		// 输入邮箱，发送重设密码连接
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		$mail = $this->kv->get('mail_conf');
		
		if(empty($mail['smtplist'])) {
			$this->message('站点没有设置 SMTP，无法使用此功能，如果有疑问请联系管理员。');
		}
		
		if(empty($this->conf['resetpw_on'])) {
			$this->message('站点没有开启密码找回的功能，如果有疑问请联系管理员。');
		}
		
		$error = array();
		$email = '';
		if($this->form_submit()) {
			$email = core::gpc('email', 'P');
			// 发送验证邮箱，todo:如何防止重复不停的发送？每天只能找回一次。tmp 目录保存今日的 username，每日计划任务清空一次
			// 保存用户 uid
			// 发送链接
			$userdb = $this->user->get_user_by_email($email);
			if(empty($userdb)) {
				$userdb = $this->user->get_user_by_username($email);
				if(empty($userdb)) {
					$error['email'] = '用户名/Email 不存在';
					$this->message($error);
				}
			}
			$username = $userdb['username'];
			$time = substr($_SERVER['time'], 0, -4);
			$verify = md5($username.$time.md5($this->conf['auth_key']));
			
			$username_url = core::urlencode($username);
			$url = "?user-resetpw2-username-$username_url-verify-$verify.htm";
			
			$emailarr = explode('@',$email);
			$emailinfo = $this->mmisc->get_email_site($emailarr[1]);	
			$email_smtp_url = $emailinfo['url'];
			$email_smtp_name = $emailinfo['name'];
			
			$subject = "您的找回密码邮件！-".$this->conf['app_name'];
			$message = "
				<html>
					<head>
						<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
					</head>
					<body>
						<h2>尊敬的用户 $username, 您好！</h2>
						<h3>点击以下链接找回您在【".$this->conf['app_name']."】的密码（该链接有效时间：三小时）：<br /><a href=\"$url\" target=\"_blank\">$url</a></h3>
					</body>
				</html>
			";
			$emailsend = $this->mmisc->sendmail($username, $email, $subject, $message);
			if(empty($emailsend)) {
				$this->message("密码重设邮件发送成功！<a href=\"$email_smtp_url\" target=\"_blank\">请点击进入【{$email_smtp_name}】查收</a>", 1, $email_smtp_url);
			} else {
				$this->message("发送邮件碰到了错误：$emailsend", 0);
			}
		}
		
		$this->view->assign('email', $email);
		$this->view->assign('error', $error);
		$this->view->display('user_resetpw.htm');
	}
	
	// 邮箱跳转过来
	public function on_resetpw2() {
		$username_url = core::gpc('username');
		$username = core::urldecode($username_url);
		$verify = core::gpc('verify');
		$time = substr($_SERVER['time'], 0, -4);
		$verify2 = md5($username.$time.md5($this->conf['auth_key']));
		
		if($verify != $verify2) {
			$this->message('链接可能已经过期，请返回。', 0);
		}
		
		// 重设密码！给出重设密码的表单！
		$error = array();
		if($this->form_submit()) {
			$password = core::gpc('password', 'P');
			$password2 = core::gpc('password2', 'P');
			$error['password'] = $this->user->check_password($password);
			if(empty($error['password'])) {
				if($password != $password2) {
					$error['password2'] = '两次输入的不一致';
				} else {
					// 设置密码
					$user = $this->user->get_user_by_username($username);
					if(empty($user)) {
						$this->message('该用户不存在！', 0);
					} else {
						$user['password'] = $this->user->md5_md5($password, $user['salt']);
						$this->user->update($user);
						$error = array();
						
						// 重新设置 cookie
						$this->user->set_login_cookie($user);
						$this->message($user['username'].'，您好，修改密码成功！', 1, './');
					}
				}
			}
		}
		
		$this->view->assign('username_url', $username_url);
		$this->view->assign('username', $username);
		$this->view->assign('verify', $verify);
		$this->view->assign('email', $email);
		$this->view->assign('error', $error);
		$this->view->display('user_resetpw2.htm');
		
	}
	
	// email 激活
	public function on_active() {
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		$code = core::gpc('code');
		if(empty($code)) {
			$this->message('缺少参数 code。');
		}
		$s = decrypt($code, $this->conf['auth_key']);
		$arr = explode("\t",  $s);
		if(empty($arr) || empty($s)) {
			$this->message('参数解密错误。');
		}
		
		$uid = $arr[0];
		$time = $arr[1];
		if($_SERVER['time'] - $time > 86400) {
			$this->message('激活链接已经过期（超过一天），请重新注册。');
		}
		
		$user = $this->user->read($uid);
		$this->check_user_exists($user);
		if($user['groupid'] != 6) {
			$this->message('您的账户已经激活，不需要重新激活。');
		}
		$user['groupid'] = 11;
		$this->user->update($user);
		
		// hook user_active_after.php
		
		// 手工设置 cookie.
		$this->user->set_login_cookie($user);
		
		$this->message($user['username'].'，您好！您的账号激活成功！', 1, $this->conf['app_url']);
	}
	
	public function on_uploadavatar() {
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		$uid = $this->_user['uid'];
		$this->check_login();
		
		$this->check_forbidden_group();
		$user = $this->user->read($uid);
		if(!$this->check_user_access($user, 'attach', $message)) {
			$this->message($message, 0);
		}
		
		// hook user_uploadavatar_before.php
		
		$dir = image::set_dir($uid, $this->conf['upload_path'].'avatar/');
		$destfile = $this->conf['upload_path']."avatar/$dir/{$uid}_tmp.jpg";
		$desturl = $this->conf['upload_url']."avatar/$dir/{$uid}_tmp.jpg?".$_SERVER['time'];
		
		if(empty($_FILES['Filedata']['tmp_name'])) {
			$this->message('请上传图片！', 0);
		}
		if(!$this->attach->is_safe_image($_FILES['Filedata']['tmp_name'])) {
			$this->message('系统检测到你上传的图片不是安全的，请更换其他图片试试！', 0);
		}
				
		$arr = image::thumb($_FILES['Filedata']['tmp_name'], $destfile, 800, 800);
		$json = array('width'=>$arr['width'], 'height'=>$arr['height'], 'body'=>$desturl);
		
		// hook user_uploadavatar_after.php
		$this->message($json, 1);
	}
	
	public function on_clipavatar() {
		
		// 检查IP 屏蔽
		$this->check_ip();
		
		$uid = $this->_user['uid'];
		$this->check_login();
		$user = $this->user->read($uid);
		$this->check_user_exists($user);
		
		$x = intval(core::gpc('x', 'P'));
		$y = intval(core::gpc('y', 'P'));
		$w = intval(core::gpc('w', 'P'));
		$h = intval(core::gpc('h', 'P'));
		$dir = image::get_dir($uid);
		
		$srcfile = $this->conf['upload_path']."avatar/$dir/$user[uid]_tmp.jpg";
		$tmpfile = $this->conf['upload_path']."avatar/$dir/$user[uid]_tmp_tmp.jpg";
		$smallfile = $this->conf['upload_path']."avatar/$dir/$user[uid]_small.gif";
		$middlefile = $this->conf['upload_path']."avatar/$dir/$user[uid]_middle.gif";
		$bigfile = $this->conf['upload_path']."avatar/$dir/$user[uid]_big.gif";
		$hugefile = $this->conf['upload_path']."avatar/$dir/$user[uid]_huge.gif";
		$hugeurl = $this->conf['upload_url']."avatar/$dir/$user[uid]_huge.gif?".$_SERVER['time'];
		
		// hook user_clipavatar_before.php
		
		image::clip($srcfile, $tmpfile, $x, $y, $w, $h);
		
		image::thumb($tmpfile, $smallfile, $this->conf['avatar_width_small'], $this->conf['avatar_width_small']);
		image::thumb($tmpfile, $middlefile, $this->conf['avatar_width_middle'], $this->conf['avatar_width_middle']);
		image::thumb($tmpfile, $bigfile, $this->conf['avatar_width_big'], $this->conf['avatar_width_big']);
		image::thumb($tmpfile, $hugefile, $this->conf['avatar_width_huge'], $this->conf['avatar_width_huge']);
		
		unlink($srcfile);
		unlink($tmpfile);
		
		if(is_file($middlefile)) {
			$user['avatar'] = $_SERVER['time'];
			$this->user->update($user);
			
			// hook user_clipavatar_after.php
			$this->message($hugeurl, 1);
		} else {
			$this->message('保存失败', 0);
		}
	}
	
	// 检测用户是否可以注册
	public function on_checkname() {
		$username = core::urldecode(core::gpc('username'));
		$error = $this->user->check_username($username);
		empty($error) && $error = $this->user->check_username_exists($username);
		if(empty($error)) {
			$this->message('可以注册', 1);
		} else {
			$this->message($error, 0);
		}
	}
	
	// 检测 email 是否已经被注册
	public function on_checkemail() {
		$email = core::urldecode(core::gpc('email'));
		$emailerror = $this->user->check_email($email);
		if($emailerror) {
			$this->message($emailerror, 0);
		}
		$error = $this->user->check_email_exists($email);
		if(empty($error)) {
			$this->message('可以注册', 1);
		} else {
			$this->message('已经被注册', 0);
		}
	}
	
	// 发送激活连接 $user[username]
	private function send_active_mail($uid, $username, $email, &$error) {
		$emailarr = explode('@',$email);
		$emailinfo = $this->mmisc->get_email_site($emailarr[1]);	
		$error['email_smtp_url'] = $emailinfo['url'];
		$error['email_smtp_name'] = $emailinfo['name'];
		$code = encrypt("$uid	$_SERVER[time]", $this->conf['auth_key']);
		$url = "?user-active-code-$code.htm";
		$subject = '请激活您在'.$this->conf['app_name'].'注册的账号！';
		$message = "
			<html>
				<head>
					<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
				</head>
				<body>
					尊敬的用户 {$username}，您好！<br />
					您在本站注册的账号还需一步完成注册，请点击以下链接激活您的账号：<br />
					<a href=\"$url\">$url</a>
				</body>
			</html>";
		// hook user_send_active_mail_after.php
		$error['emailsend'] = $this->mmisc->sendmail($username, $email, $subject, $message);
	}
	
	private function get_referer() {
		$referer = core::gpc('HTTP_REFERER', 'S');
		(empty($referer) || strpos($referer, 'user-login') !== FALSE || strpos($referer, 'user-logout') !== FALSE ||  strpos($referer, 'user-create') !== FALSE) && $referer = $this->conf['app_url'];
		return $referer;
	}
	// hook user_control_after.php
	
}

?>