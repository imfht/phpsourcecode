<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: hooks.class.php 2015-5-13 15:24:06Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_qq_base {

	public $setting;

	function init() {
		global $_G;
		include_once template('qq:module');
		if (!$_G['cache']['plugin']['qq']['isopen'] || $_G['setting']['bbclosed']) {
			return;
		}
		$this->allow = true;
	}

	function common_base() {
		global $_G;

		$this->setting = $_G['cache']['plugin']['qq'];
		$this->setting['isopen'] = intval($this->setting['isopen']);
		$this->setting['onlyallowbind'] = intval($this->setting['onlyallowbind']);
		$this->setting['callback_domain'] = trim($this->setting['callback_domain']);
		$this->setting['appid'] = trim($this->setting['appid']);
		$this->setting['appkey'] = trim($this->setting['appkey']);
		$this->setting['newusergroupid'] = intval($this->setting['newusergroupid']);
		$this->setting['disableregrule'] = intval($this->setting['disableregrule']);
		$this->setting['register_birthday'] = intval($this->setting['register_birthday']);
		$this->setting['register_gender'] = intval($this->setting['register_gender']);
		$this->setting['register_uinlimit'] = trim($this->setting['register_uinlimit']);
		$this->setting['register_rewardcredit'] = intval($this->setting['register_rewardcredit']);
		$this->setting['register_addcredit'] = trim($this->setting['register_addcredit']);
		$this->setting['guest_groupid'] = intval($this->setting['guest_groupid']);
		$this->setting['register_regverify'] = intval($this->setting['register_regverify']);
		$this->setting['register_invite'] = trim($this->setting['register_invite']);

		$this->setting['callback_domain'] = $this->setting['callback_domain'] ? "http://" . rtrim(str_replace("http://", "", $this->setting['callback_domain']), "/") . "/" : $_G['siteurl'];

		$_G['qqlogin'] = array();
		$_G['qqlogin']['referer'] = !$_G['inajax'] && CURSCRIPT != 'member' ? $_G['basefilename'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '') : dreferer();
		$_G['qqlogin']['login_url'] = $this->setting['callback_domain'] . 'qq.php?mod=login&referer=' . urlencode($_G['qqlogin']['referer'] ? $_G['qqlogin']['referer'] : 'index.php');
		$_G['connect']['discuz_change_qq_url'] = $_G['siteurl'] . 'qq.php?mod=login&op=change';

		if ($_G['uid']) {
			dsetcookie('connect_is_bind', $_G['member']['conisbind'], 31536000);
			if (!$_G['member']['conisbind'] && $_G['cookie']['connect_login']) {
				$_G['cookie']['connect_login'] = 0;
				dsetcookie('connect_login');
			}
		}

		if (!$_G['uid'] && $_G['connectguest']) {
			if ($_G['cookie']['connect_qq_nick']) {
				$_G['member']['username'] = $_G['cookie']['connect_qq_nick'];
			} else {
				$connectGuest = C::t('#qq#qq_member_guest')->fetch($conopenid);
				if ($connectGuest['nickname']) {
					$_G['member']['username'] = $connectGuest['nickname'];
				}
			}
		}

		if ($this->allow && !$_G['uid'] && !defined('IN_MOBILE')) {
			$_G['setting']['pluginhooks']['global_login_text'] = tpl_login_bar();
		}
	}

}

class plugin_qq extends plugin_qq_base {

	var $allow = false;

	function plugin_qq() {
		$this->init();
	}

	function common() {
		global $_G;
		$this->common_base();
		$_G['setting']['connect']['allow'] = 1;
		if (!$this->setting['onlyallowbind']) {
			$_G['setting']['regconnect'] = 1;
		}
	}


	function global_login_extra() {
		global $_G;
		if (!$this->setting['isopen']) {
			return;
		}
		return tpl_global_login_extra();
	}

	function global_usernav_extra1() {
		global $_G;

		if (!$_G['uid'] && !$_G['connectguest']) {
			return;
		}
		if (!$_G['member']['conisbind']) {
			return tpl_global_usernav_extra1();
		}
	}

}

class plugin_qq_member extends plugin_qq {

	function qq_member() {

		global $_G, $seccodecheck, $secqaacheck, $connect_guest;

		if ($this->allow) {

			if ($_G['uid'] && $_G['member']['conisbind']) {
				dheader('location: ' . $_G['siteurl'] . 'index.php');
			}

			$connect_guest = array();

			if ($_G['connectguest'] && (submitcheck('regsubmit', 0, $seccodecheck, $secqaacheck) || submitcheck('loginsubmit', 1, $seccodestatus))) {
				if (!$_GET['auth_hash']) {
					$_GET['auth_hash'] = $_G['cookie']['con_auth_hash'];
				}
				$conopenid = authcode($_GET['auth_hash']);
				$connect_guest = C::t('#qq#qq_member_guest')->fetch($conopenid);

				if (!$connect_guest) {
					dsetcookie('con_auth_hash');
					showmessage('qq:connect_login_first');
				}
			}
		}
	}

	function logging_member() {
		global $_G;
		if ($this->allow && $_G['connectguest'] && $_GET['action'] == 'login') {
			if ($_G['inajax']) {
				showmessage('qq:connectguest_message_complete_or_bind');
			} else {
				dheader('location: ' . $_G['siteurl'] . 'member.php?mod=qq&ac=bind');
			}
		}
	}

	function register_member() {
		global $_G;
		if ($this->allow && $_G['connectguest']) {
			if ($_G['inajax']) {
				showmessage('qq:connectguest_message_complete_or_bind');
			} else {
				dheader('location: ' . $_G['siteurl'] . 'member.php?mod=qq');
			}
		}
	}

	function logging_method() {
		if (!$this->allow) {
			return;
		}
		return tpl_login_bar();
	}

	function register_logging_method() {
		if (!$this->allow) {
			return;
		}
		return tpl_login_bar();
	}

	function qq_input_output() {
		if (!$this->allow) {
			return;
		}
		global $_G;
		$_G['setting']['pluginhooks']['register_input'] = tpl_register_input();
	}

	function qq_bottom_output() {
		if (!$this->allow) {
			return;
		}
		global $_G;
		$_G['setting']['pluginhooks']['register_bottom'] = tpl_register_bottom();
	}

}

?>