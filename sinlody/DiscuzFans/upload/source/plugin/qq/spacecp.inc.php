<?php

/**
 * Wikin! [ Discuz!应用专家，维清互联旗下最新品牌 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: spacecp.inc.php 2016-4-10 18:46:03Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT . './source/plugin/qq/config/config.php';

if (empty($_G['uid'])) {
	showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}

$op = !empty($_GET['op']) ? $_GET['op'] : '';


$_G['qqlogin']['loginbind_url'] = $_G['siteurl'] . 'qq.php?mod=login&op=init&type=loginbind&referer=' . urlencode($_G['qqlogin']['referer'] ? $_G['qqlogin']['referer'] : 'index.php');

$qq_member = C::t('#qq#qq_member')->fetch($_G['uid']);
$_G['member'] = array_merge($_G['member'], $qq_member);

if (submitcheck('connectsubmit')) {

	if ($op == 'config') { // debug 修改QQ绑定设置
		$ispublisht = !empty($_GET['ispublisht']) ? 1 : 0;
		C::t('#qq#common_member_connect')->update($_G['uid'], array(
			'conispublisht' => $ispublisht,
				)
		);
		if (!$ispublisht) {
			dsetcookie('connect_synpost_tip');
		}
		showmessage('qq:connect_config_success', $referer);
	} elseif ($op == 'unbind') {

		if ($qq_member['uid']) {

			if ($_G['member']['conisregister']) {
				if ($_G['setting']['strongpw']) {
					$strongpw_str = array();
					if (in_array(1, $_G['setting']['strongpw']) && !preg_match("/\d+/", $_GET['newpassword1'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_1');
					}
					if (in_array(2, $_G['setting']['strongpw']) && !preg_match("/[a-z]+/", $_GET['newpassword1'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_2');
					}
					if (in_array(3, $_G['setting']['strongpw']) && !preg_match("/[A-Z]+/", $_GET['newpassword1'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_3');
					}
					if (in_array(4, $_G['setting']['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $_GET['newpassword1'])) {
						$strongpw_str[] = lang('member/template', 'strongpw_4');
					}
					if ($strongpw_str) {
						showmessage(lang('member/template', 'password_weak') . implode(',', $strongpw_str));
					}
				}
				if ($_GET['newpassword1'] !== $_GET['newpassword2']) {
					showmessage('profile_passwd_notmatch', $referer);
				}
				if (!$_GET['newpassword1'] || $_GET['newpassword1'] != addslashes($_GET['newpassword1'])) {
					showmessage('profile_passwd_illegal', $referer);
				}
			}
		} else { // debug 因为老用户access token等信息，所以没法通知connect，所以直接在本地解绑就行了，不fopen connect
			if ($_G['member']['open']) {
				if ($_GET['newpassword1'] !== $_GET['newpassword2']) {
					showmessage('profile_passwd_notmatch', $referer);
				}
				if (!$_GET['newpassword1'] || $_GET['newpassword1'] != addslashes($_GET['newpassword1'])) {
					showmessage('profile_passwd_illegal', $referer);
				}
			}
		}

		C::t('#qq#qq_member')->delete($_G['uid']);

		C::t('common_member')->update($_G['uid'], array('conisbind' => 0));
		C::t('#qq#qq_member_bindlog')->insert(array('uid' => $_G['uid'], 'openid' => $qq_member['openid'], 'type' => 2, 'dateline' => time()));

		if ($_G['member']['conisregister']) {
			loaducenter();
			uc_user_edit(addslashes($_G['member']['username']), null, $_GET['newpassword1'], null, 1);
		}

		foreach ($_G['cookie'] as $k => $v) {
			dsetcookie($k);
		}

		$_G['uid'] = $_G['adminid'] = 0;
		$_G['username'] = $_G['member']['password'] = '';

		showmessage('qq:connect_config_unbind_success', 'forum.php');
	}
}
?>