<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: misc_emailcheck.php 33688 2013-08-02 03:00:15Z pmonkey_w $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$uid = 0;
$sms = '';
$_GET['hash'] = empty($_GET['hash']) ? '' : $_GET['hash'];
if($_GET['hash']) {
	list($uid, $sms, $time) = explode("\t", authcode($_GET['hash'], 'DECODE', md5(substr(md5($_G['config']['security']['authkey']), 0, 16))));
	$uid = intval($uid);
}

if($uid && issms($sms) && $time > TIMESTAMP - 86400) {
	$member = getuserbyuid($uid);
	$setarr = array('sms'=>$sms, 'smsstatus'=>'1');
	if($_G['member']['freeze'] == 2) {
		$setarr['freeze'] = 0;
	}
	loaducenter();
	$ucresult = uc_user_edit_new(addslashes($member['username']), '', '', null, $sms, 1);
	if($ucresult == -10) {
		showmessage('sms_check_account_invalid', '', array(), array('return' => true));
	} elseif($ucresult == -7) {
		showmessage('profile_sms_illegal', '', array(), array('return' => true));
	} elseif($ucresult == -8) {
		showmessage('profile_sms_duplicate', '', array(), array('return' => true));
	}
	if($_G['setting']['regverify'] == 1 && $member['groupid'] == 8) {
		$membergroup = C::t('common_usergroup')->fetch_by_credits($member['credits']);
		$setarr['groupid'] = $membergroup['groupid'];
	}
	updatecreditbyaction('realsms', $uid);
	C::t('common_member')->update($uid, $setarr);
	C::t('common_member_validate')->delete($uid);
	dsetcookie('newsms', "", -1);

	showmessage('sms_check_sucess', 'home.php?mod=spacecp&ac=profile&op=password', array('sms' => $sms));
} else {
	showmessage('sms_check_error', 'index.php');
}

?>