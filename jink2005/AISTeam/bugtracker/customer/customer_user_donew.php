<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_user_donew.php,v 1.13 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");
include("../include/feedback_email_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!trim($_POST['email'])) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST, 
						  "no_empty", "email");
}

if ($_POST['email'] && !IsEmailAddress($_POST['email'])) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST, 
						  "wrong_format", "email");
}

if (!trim($_POST['password1'])) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST, 
						  "no_empty", "password");
}

if (utf8_strlen($_POST['realname']) > 100) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST,
						  "too_long", "real_name", "100");
}
if (utf8_strlen($_POST['email']) > 50) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST,
						  "too_long", "address", "150");
}
if (utf8_strlen($_POST['password1']) > 50) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST,
						  "too_long", "tel", "20");
}

if ($_POST['password1'] != $_POST['password2']) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php?customer_id=".$_POST['customer_id'], $_POST,
						  "password_not_match");
}

if ($_POST['enable_login'] == 0) {
	$account_disabled = 't';
} else {
	$account_disabled = 'f';
}

if (($_POST['auto_cc_to'] == 1) && ($_POST['customer_id'] != 0)){
	$auto_cc_to = 't';
} else {
	$auto_cc_to = 'f';
}

// 先檢查是否有同樣 customer user (by email)
$check_user_sql="select * from ".$GLOBALS['BR_customer_user_table']." 
				where email=".$GLOBALS['connection']->QMagic($_POST['email']);

$check_user_result = $GLOBALS['connection']->Execute($check_user_sql) or 
		DBError(__FILE__.":".__LINE__);

$line = $check_user_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php", $_POST,
						  "have_same", "customer_user", $_POST['email']);
}

$now = $GLOBALS['connection']->DBTimeStamp(time());
$sql = "insert into ".$GLOBALS['BR_customer_user_table']."(customer_id,
		realname, created_date, email, password, language, auto_cc_to, account_disabled)
		values(".$GLOBALS['connection']->QMagic($_POST['customer_id']).",
		".$GLOBALS['connection']->QMagic($_POST['realname']).", $now, ".$GLOBALS['connection']->QMagic($_POST['email']).", 
		".$GLOBALS['connection']->QMagic(md5($_POST['password1'])).", ".$GLOBALS['connection']->QMagic($_POST['language']).", 
		".$GLOBALS['connection']->QMagic($auto_cc_to).", ".$GLOBALS['connection']->QMagic($account_disabled).")";

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

WriteSyslog("info", "syslog_new_xxx", "customer_user", $_POST['email']);

LoadingTimerShow();
SendUpdateCustomerUserEamil($_POST['email'], $_POST['password1'], "create");
LoadingTimerHide();

FinishPrintOut("customer_user_admin.php?customer_id=".$_POST['customer_id'], "finish_new", "customer_user", 0);

?>
