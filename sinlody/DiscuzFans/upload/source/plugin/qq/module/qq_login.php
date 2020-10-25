<?php

/**
 * Wikin! [ Discuz!应用专家，维清互联旗下最新品牌 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: qqlogin_api.php 2015-5-13 15:28:10Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$allowop = array('init', 'callback');
$op = $_GET['op'];
if (!in_array($op, $allowop)) {
	$op = 'init';
}

$referer = dreferer();
$callback_url = $_G['siteurl'] . 'qq.php?mod=login&op=callback';

try {
	$OAuth = new OAuth();
} catch (Exception $e) {
	showmessage('qq:init_otuth_error', $referer, array('Message' => $e->getmessage()));
}

if ($op == 'init') {

	$callback = $callback_url . '&referer=' . urlencode($_GET['referer']);
	dsetcookie('request_uri', $callback);
	$redirect = $OAuth->getOAuthAuthorizeURL($callback);
	dheader('Location:' . $redirect);
} elseif ($op == 'callback') {

	$params = $_GET;

	if ($_GET['state'] != md5(FORMHASH)) {
		showmessage('qq:login_get_access_token_failed', $referer);
	}

	try {
		$response = $OAuth->GetOpenId($_G['cookie']['request_uri'], $_GET['code']);
	} catch (Exception $e) {
		showmessage('qq:login_get_access_token_failed_code', $referer, array('codeMessage' => getErrorMessage($e->getmessage()), 'code' => $e->getmessage()));
	}
	$openid = $response['openid'];
	$access_token = $response['access_token'];
	if (!$openid || !$access_token) {
		showmessage('qq:login_get_access_token_failed', $referer);
	}

	$user = C::t('#qq#qq_member')->fetch_first_by_openid($openid);

	if ($_G['uid']) {
		if ($user && $user['uid'] != $_G['uid']) {
			showmessage('qq:login_get_access_token_failed', $referer, array('username' => $_G['member']['username']));
		}
		$current_qq_member = C::t('#qq#qq_member')->fetch($_G['uid']);
		if ($_G['member']['conisbind'] && $current_qq_member['openid']) {
			if ($current_qq_member['openid'] != $openid) {
				showmessage('qq:login_get_access_token_failed', $referer);
			}
			C::t("#qq#qq_member")->update(array($_G['uid'], 'openid' => $openid, 'access_token' => $access_token));
		} else {
			C::t("#qq#qq_member")->insert(array('uid' => $_G['uid'], 'openid' => $openid, 'access_token' => $access_token));
		}
		C::t('common_member')->update($_G['uid'], array('conisbind' => '1'));
		C::t('#qq#qq_member_bindlog')->insert(array('uid' => $_G['uid'], 'openid' => $openid, 'type' => 1, 'dateline' => time()));
		showmessage('qq:connect_register_bind_success', $referer);
	} else {
		if ($user) {
			C::t('#qq#qq_member')->update($user['uid'], array('access_token' => $access_token));

			$login = User::login($user);

			if (!$login) {
				showmessage('qq:login_error', $referer);
			}

			$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle']);

			C::t('common_member_status')->update($user['uid'], array('lastip' => $_G['clientip'], 'lastvisit' => TIMESTAMP, 'lastactivity' => TIMESTAMP));

			$ucsynlogin = '';
			if ($_G['setting']['allowsynlogin']) {
				loaducenter();
				$ucsynlogin = uc_user_synlogin($_G['uid']);
			}

			showmessage('login_succeed', $referer, $param, array('extrajs' => $ucsynlogin));
		} else {
            $connectGuest = C::t('#qq#qq_member_guest')->fetch($openid);
			if ($connectGuest['nickname']) {
		                $figureurl = $connectGuest['figureurl_qq'];
		                $data = array(
		                    'openid' => $response['openid'],
		                    'access_token' => $response['access_token'],
		                    'nickname' => $connectGuest['nickname'],
		                    'gender' => $connectGuest['gender'],
		                    'province' => $connectGuest['province'],
		                    'city' => $connectGuest['city'],
		                    'figureurl_qq' => $figureurl,
		                );
			} else {

				try {
					$userinfo = $OAuth->getUserInfo($openid, $access_token);
					if (CHARSET == 'gbk') {
						foreach ($userinfo as $key => $value) {
							$userinfo[$key] = diconv($value, 'UTF-8');
						}
					}
				} catch (Exception $e) {
					showmessage('qq:openapi_error', $referer, array('Message' => $e->getmessage()));
				}

				$figureurl = !empty($userinfo['figureurl_qq_2']) ? $userinfo['figureurl_qq_2'] : $userinfo['figureurl_qq_1'];

				$data = array(
					'openid' => $response['openid'],
					'access_token' => $response['access_token'],
					'nickname' => $userinfo['nickname'],
					'gender' => $userinfo['gender'],
					'province' => $userinfo['province'],
					'city' => $userinfo['city'],
					'figureurl_qq' => $figureurl,
				);
			}

			C::t('#qq#qq_member_guest')->insert($data, false, true);

			if ($data['nickname']) {
				dsetcookie('connect_qq_nick', $data['nickname'], 86400);
			}

			$auth_hash = authcode($response['openid'], 'ENCODE');
			dsetcookie('con_auth_hash', $auth_hash, 86400);
			dsetcookie('connect_js_name', 'guest_ptlogin', 86400);
			dsetcookie('stats_qc_login', 4, 86400);

			if ($figureurl) {
				dsetcookie('connect_qq_figureurl', $figureurl, 86400);
			}

			dheader("Location:member.php?mod=qq");
		}
	}
}
?>