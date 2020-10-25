<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: check.php 34702 2014-07-10 10:08:30Z nemohou $
 */

if(!defined('IN_MOBILE_API')) {
	exit('Access Denied');
}

require './source/class/class_core.php';

$discuz = C::app();
$discuz->init();

if(!defined('DISCUZ_VERSION')) {
	require './source/discuz_version.php';
}

if(in_array('mobile', $_G['setting']['plugins']['available'])) {
    $array = array(
	    'discuzversion' => DISCUZ_VERSION,
	    'charset' => CHARSET,
	    'version' => MOBILE_PLUGIN_VERSION,
	    'pluginversion' => $_G['setting']['plugins']['version']['mobile'],
	    'regname' => $_G['setting']['regname'],
	    'qqconnect' => in_array('qqconnect', $_G['setting']['plugins']['available']) ? '1' : '0',
	    'sitename' => $_G['setting']['bbname'],
	    'mysiteid' => $_G['setting']['my_siteid'],
	    'ucenterurl' => $_G['setting']['ucenterurl']
    );
} else {
    $array = array();
}

$data = mobile_core::json($array);
mobile_core::make_cors($_SERVER['REQUEST_METHOD'], REQUEST_METHOD_DOMAIN);

echo $data;

?>