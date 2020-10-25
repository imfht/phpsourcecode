<?php
/**
 * 手动添加熊掌号号
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');

$uniacid = intval($_GPC['uniacid']);
$step = intval($_GPC['step']) ? intval($_GPC['step']) : 1;

$user_create_account_info = permission_user_account_num();
$_W['breadcrumb'] = '新建平台账号';
if (1 == $step) {
	if ($user_create_account_info['xzapp_limit'] <= 0 && !$_W['isfounder']) {
		$authurl = "javascript:alert('创建熊掌号已达上限');";
	}

	if (empty($authurl) && !empty($_W['setting']['platform']['authstate'])) {
		$authurl = "javascript:alert('暂不支持授权接入');";
	}
}

if (2 == $step) {
	//新建平台基本信息. 新路由 account/create/base_info  &sign=xzapp
}
if (3 == $step) {
	//新建平台分配权限. 新路由 account/create/account_modules &uniacid=
}

if (4 == $step) {
	$uniacid = intval($_GPC['uniacid']);
	$uni_account = table('uni_account')
		->where(array('uniacid' => $uniacid))
		->get();
	if (empty($uni_account)) {
		itoast('非法访问', '', '');
	}
	$owner_info = account_owner($uniacid);
	if (!(user_is_founder($_W['uid'], true) || $_W['uid'] == $owner_info['uid'])) {
		itoast('非法访问');
	}
	$account = account_fetch($uni_account['default_acid']);
}

template('xzapp/post-step');
