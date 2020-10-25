<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_user_doedit.php,v 1.14 2013/07/07 21:28:25 alex Exp $
 *
 */
include("../include/header.php");
include("../include/feedback_email_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
	
if (!$_POST['customer_user_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_user_id");
}

$sql = "select customer_id from ".$GLOBALS['BR_customer_user_table']." where customer_user_id=".$GLOBALS['connection']->QMagic($_POST['customer_user_id']);
$result = $GLOBALS['connection']->Execute($sql);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer_user", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer_user");
}

if (!trim($_POST['email'])) {
	ErrorPrintOut("no_empty", "email");
}

if (!trim($_POST['password1'])) {
	ErrorPrintOut("no_empty", "password");
}

if (utf8_strlen($_POST['realname']) > 100) {
	ErrorPrintOut("too_long", "realname", "100");
}

if (utf8_strlen($_POST['email']) > 50) {
	ErrorPrintOut("too_long", "email", "50");
}

if (!IsEmailAddress($_POST['email'])) {
	ErrorPrintOut("wrong_format", "email");
}

if (utf8_strlen($_POST['password1']) > 50) {
	ErrorPrintOut("too_long", "password", "50");
}


// Check whether there is the same customer user (by email)
$check_user_sql="select * from ".$GLOBALS['BR_customer_user_table']." 
				where email=".$GLOBALS['connection']->QMagic($_POST['email'])."
				and customer_user_id!=".$GLOBALS['connection']->QMagic($_POST['customer_user_id']);

$check_user_result = $GLOBALS['connection']->Execute($check_user_sql) or 
		DBError(__FILE__.":".__LINE__);

$line = $check_user_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "customer_user_new.php", $_POST,
						  "have_same", "customer_user", $_POST['email']);
}

if (($_POST['password1'] == "12345678") && ($_POST['password2'] == "AlexWang") ){
	$change_password = 0;
} else {
	$change_password = 1;
}

if (($change_password) && 
	($_POST['password1'] != $_POST['password2']) ) {
	ErrorPrintOut("password_not_match");
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

$sql = "update ".$GLOBALS['BR_customer_user_table']." set customer_id=".$GLOBALS['connection']->QMagic($_POST['customer_id']).", 
		realname=".$GLOBALS['connection']->QMagic($_POST['realname']).",
		email=".$GLOBALS['connection']->QMagic($_POST['email']).", language=".$GLOBALS['connection']->QMagic($_POST['language']).", 
		account_disabled='".$account_disabled."', 
		auto_cc_to=".$GLOBALS['connection']->QMagic($auto_cc_to)." where customer_user_id=".$GLOBALS['connection']->QMagic($_POST['customer_user_id']);

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

if ($change_password) {
	$passwd_sql = "update ".$GLOBALS['BR_customer_user_table']." 
		set password='".md5($_POST['password1'])."' where customer_user_id=".$GLOBALS['connection']->QMagic($_POST['customer_user_id']);
	$GLOBALS['connection']->Execute($passwd_sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_edit_xxx", "customer_user", $_POST['email']);

if ($change_password) {
	LoadingTimerShow();
	SendUpdateCustomerUserEamil($_POST['email'], $_POST['password1'], "password");
	LoadingTimerHide();
}

FinishPrintOut("customer_user_admin.php?customer_id=".$_POST['customer_id'], "finish_update", "customer_user", 0);
?>
