<?php

/**
 * [WeEngine System] Copyright (c) 2014 W7.CC
 * 获取模块入口信息.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('module');

$modulename = trim($_GPC['modulename']);
$callname = trim($_GPC['callname']);
$uniacid = intval($_GPC['uniacid']);
$_W['uniacid'] = intval($_GPC['uniacid']);

$args = $_GPC['args'];
$module_info = module_fetch($modulename);
//模块不存在返回空
if (empty($module_info)) {
	iajax(0, array());
}
$site = WeUtility::createModuleSite($modulename);
if (empty($site)) {
	iajax(0, array());
}
//call不存在返回空
if (!method_exists($site, $callname)) {
	iajax(0, array());
}
$ret = @$site->$callname($args);
iajax(0, $ret);
