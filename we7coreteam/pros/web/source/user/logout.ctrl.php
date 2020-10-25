<?php
/**
 * 退出系统
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');
isetcookie('__session', '', -10000);
isetcookie('__iscontroller', '', -10000);
$forward = $_GPC['forward'];
if (empty($forward)) {
	$forward = $_W['siteroot'];
}
header('Location:' . $forward);
