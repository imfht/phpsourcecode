<?php
/**
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('cloud');

$dos = array('sms_sign', 'sign_remove', 'display');
$do = in_array($do, $dos) ? $do : 'display';

if ('sms_sign' == $do) {
	$data = cloud_sms_sign(intval($_GPC['page']), intval($_GPC['start_time']), intval($_GPC['end_time']), intval($_GPC['status_audit']), intval($_GPC['status_order']));
	if (isset($data['data'][0]['createtime']) && is_numeric($data['data'][0]['createtime'])) {
		foreach ($data['data'] as &$item) {
			$item['createtime'] = date('Y-m-d H:i:s', $item['createtime']);
		}
	}
	$data['page'] = $data['current_page'];
	$data['page_size'] = $data['per_page'];
	iajax(0, $data);
}
if ('sign_remove' == $do) {
	$sign_id = intval($_GPC['sign_id']);
	$result = cloud_sms_remove($sign_id);
	if (is_error($result)) {
		iajax(-1, $result['message']);
	}
	iajax(0, '删除成功!');
}
template('cloud/sms-sign');
