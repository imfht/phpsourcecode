<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: optimizer_smsregister.php 33906 2013-08-29 09:40:37Z jeffjzhang $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class optimizer_smsregister {

	public function __construct() {

	}

	public function check() {
		$sendregister = C::t('common_setting')->fetch('sendregisterverify');
		if($sendregister == 2) {
			$return = array('status' => 2, 'type' =>'header', 'lang' => lang('optimizer', 'optimizer_smsregister_normal'), 'extraurl' => '&checkmobile=1');
		} else {
			$return = array('status' => 2, 'type' =>'header', 'lang' => lang('optimizer', 'optimizer_smsregister_tip'));
		}
		return $return;
	}

	public function optimizer() {
		$adminfile = defined(ADMINSCRIPT) ? ADMINSCRIPT : 'admin.php';
		if($_GET['checksms']) {
			$url = '?action=setting&operation=sms';
		} else {
			$url = '?action=setting&operation=access';
		}
		dheader('Location: '.$_G['siteurl'].$adminfile.$url);
	}
}

?>