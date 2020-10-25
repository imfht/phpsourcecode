<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: misc_buyinvitecode.php 31572 2012-09-10 08:59:03Z zhengqingpeng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(submitcheck('buysubmit')) {
	if($_G['setting']['ec_tenpay_bargainor'] || $_G['setting']['ec_wxpay_appid'] || $_G['setting']['ec_tenpay_opentrans_chnid'] || $_G['setting']['ec_account']) {
	    $language = lang('forum/misc');
		$amount = intval($_GET['amount']);
		$email = dhtmlspecialchars($_GET['email']);
		$sms = dhtmlspecialchars($_GET['sms']);
		if(empty($amount)) {
			showmessage('buyinvitecode_no_count', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if($email && (strlen($email) < 6 || !preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email))) {
			showmessage('buyinvitecode_email_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if($sms && (strlen($sms) != 11 || !preg_match("/^1\d{10}$/", $sms))) {
		    showmessage('buyinvitecode_sms_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		if(empty($email) && empty($sms)){
		    showmessage('buyinvitecode_input_empty', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}

		$price = round($amount * $_G['setting']['inviteconfig']['invitecodeprice'], 2);
		$orderid = '';

		$apitype = is_numeric($_GET['bank_type']) ? 'tenpay' : $_GET['bank_type'];
		if(empty($apitype)) {
			showmessage('parameters_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		require_once libfile('function/trade');
		$requesturl = invite_payurl($amount, $price, $orderid, $_GET['bank_type']);

		if(C::t('forum_order')->fetch($orderid)) {
			showmessage('credits_addfunds_order_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
		}
		C::t('forum_order')->insert(array(
			'orderid' => $orderid,
			'status' => '1',
			'uid' => 0,
			'amount' => $amount,
			'price' => $price,
			'submitdate' => $_G['timestamp'],
			'email' => $email,
			'sms' => $sms,
			'ip' => $_G['clientip'],
		));
		if($_GET['bank_type'] == 'wxpay'){
		    include template('common/header_ajax');
			echo '<script type="text/javascript">showWindow(\'wxpay_qrcode_box\',\'misc.php?mod=buyinvitecode&op=wxqrcode&url='.urlencode(authcode($requesturl."\t".$orderid, 'ENCODE')).'\',\'get\',0);</script>';
			include template('common/footer_ajax');
		}else{
		    include template('common/header_ajax');
		    echo '<form id="payform" action="'.$requesturl.'" method="post"></form><script type="text/javascript" reload="1">$(\'payform\').submit();</script>';
		    include template('common/footer_ajax');
		}
		dexit();
	} else {
		showmessage('action_closed', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
	}
}elseif($_GET['op']=='wxqrcode'){
    if($_G['setting']['ec_wxpay_appid']){
        $success_url = "member.php?mod={$_G['setting']['regname']}";
        $request = authcode($_GET['url'], 'DECODE');
        $res = explode("\t", $request);
        if(empty($res) || count($res) != 2){
            showmessage('submitcheck_error', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
        }
        $requesturl = $res[0];
        $orderid = $res[1];
        $check_url = 'misc.php?mod=buyinvitecode&op=wxcheck&orderid='.urlencode(authcode($orderid, 'ENCODE'));
        include template('common/header_ajax');
        include template('common/wxpaycode');
        include template('common/footer_ajax');
        dexit();
    }else{
        showmessage('action_closed', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
    }
}elseif($_GET['op']=='wxcheck'){
    if($_G['setting']['ec_wxpay_appid']){
        $orderid = authcode($_GET['orderid'], 'DECODE');
        $order = C::t('forum_order')->fetch($orderid);
        if(empty($order)){
            showmessage('credits_addfunds_order_invalid', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
        }
        if($order['status']==2){
            $requesturl = 'misc.php?mod=buyinvitecode&action=paysucceed&orderid='.$orderid;
            include template('common/header_ajax');
            echo '<form id="payform" action="'.$requesturl.'" method="post"></form><script type="text/javascript" reload="1">$(\'payform\').submit();</script>';
            include template('common/footer_ajax');
        }
        dexit();
    }else{
        showmessage('action_closed', '', array(), array('showdialog' => 1, 'showmsg' => true, 'closetime' => true));
    }
}
if($_GET['action'] == 'paysucceed' && $_GET['orderid']) {
	$orderid = $_GET['orderid'];
	$order = C::t('forum_order')->fetch($orderid);
	if(!$order) {
		showmessage('parameters_error');
	}
	$codes = array();
	foreach(C::t('common_invite')->fetch_all_orderid($orderid) as $code) {
		$codes[] = $code['code'];
	}
	if(empty($codes)) {
		showmessage('buyinvitecode_no_id');
	}
	$codetext = implode("\r\n", $codes);
}

if($_G['group']['maxinviteday']) {
	$maxinviteday = time() + 86400 * $_G['group']['maxinviteday'];
} else {
	$maxinviteday = time() + 86400 * 10;
}
$maxinviteday = dgmdate($maxinviteday, 'Y-m-d H:i');
$_G['setting']['inviteconfig']['invitecodeprompt'] = nl2br($_G['setting']['inviteconfig']['invitecodeprompt']);

include template('common/buyinvitecode');
?>