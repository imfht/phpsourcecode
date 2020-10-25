<?php

/**
 * 云服务诊断
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');

load()->classs('cloudapi');
load()->model('cloud');
load()->model('setting');

$dos = array('display', 'testapi');
$do = in_array($do, $dos) ? $do : 'display';
permission_check_account_user('system_cloud_diagnose');

if ('testapi' == $do) {
	$starttime = microtime(true);
	$response = cloud_request('http://api-upgrade.w7.cc', array(), array('ip' => $_GPC['ip']));
	$endtime = microtime(true);
	iajax(0, '请求接口成功，耗时 ' . (round($endtime - $starttime, 5)) . ' 秒');
} else {
	if ($_W['ispost']){
		if ($_GPC['submit']) {
			$result = cloud_reset_siteinfo();
			$api = new CloudApi();
			$api->deleteCer();

			if (is_error($result)) {
				itoast($result['message'], '', 'error');
			} else {
				itoast('重置成功', 'refresh', 'success');
			}
		}
		if ($_GPC['updateserverip']) {
			if (!empty($_GPC['ip'])) {
				setting_save(array('ip' => $_GPC['ip'], 'expire' => TIMESTAMP + 201600), 'cloudip');
			} else {
				setting_save(array(), 'cloudip');
			}
			itoast('修改云服务ip成功.', 'refresh', 'success');
		}
	}
	if (empty($_W['setting']['site'])) {
		$_W['setting']['site'] = array();
	}
	$checkips = array();
	if (!empty($_W['setting']['cloudip']['ip'])) {
		$checkips[] = $_W['setting']['cloudip']['ip'];
	}
	if (strexists(strtoupper(PHP_OS), 'WINNT')) {
		$cloudip = gethostbyname('api-upgrade.w7.cc');
		if (!in_array($cloudip, $checkips)) {
			$checkips[] = $cloudip;
		}
	} else {
		for ($i = 0; $i <= 10; ++$i) {
			$cloudip = gethostbyname('api-upgrade.w7.cc');
			if (!in_array($cloudip, $checkips)) {
				$checkips[] = $cloudip;
			}
		}
	}
	template('cloud/diagnose');
}
