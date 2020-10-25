<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: member_connect_logging.php 33543 2013-07-03 06:01:33Z nemohou $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (!empty($_POST)) {
	if ($result['member']['conisbind']) {
		showmessage('qq:connect_register_bind_already');
	}
	if ($result['member']['groupid'] == 8) {
		showmessage('qq:connect_register_bind_need_inactive');
	}

	$conopenid = $this->connect_guest['openid'];
	$access_token = $this->connect_guest['access_token'];

	$user_auth_fields = 1;
	$conispublishfeed = 0;
	$conispublisht = 0;

	$is_use_qqshow = !empty($_GET['use_qqshow']) ? 1 : 0;

	if ($conopenid) {

		$data = array('uid' => $uid,
			'openid' => $conopenid,
			'access_token' => $access_token,
			'conispublishfeed' => $conispublishfeed,
			'conispublisht' => $conispublisht,
			'conisregister' => '0',
			'conisqzoneavatar' => '0',
			'conisfeed' => $user_auth_fields,
			'conisqqshow' => $is_use_qqshow
		);
		C::t('#qq#qq_member')->insert($data, false, true);
		C::t('common_member')->update($uid, array('conisbind' => '1'));
		C::t('#qq#qq_member_bindlog')->insert(array('uid' => $uid, 'openid' => $conopenid, 'type' => '1', 'dateline' => $_G['timestamp']));

		C::t('#qq#qq_member_guest')->delete($conopenid);

		dsetcookie('connect_js_name', 'user_bind', 86400);
		dsetcookie('connect_js_params', base64_encode(serialize(array('type' => 'registerbind'))), 86400);

		dsetcookie('connect_login', 1, 31536000);
		dsetcookie('connect_is_bind', '1', 31536000);
		dsetcookie('connect_uin', $conopenid, 31536000);
		dsetcookie('stats_qc_reg', 2, 86400);
		if ($_GET['is_feed']) {
			dsetcookie('connect_synpost_tip', 1, 31536000);
		}
	} else {
		showmessage('qq:connect_get_access_token_failed', dreferer());
	}
}
?>