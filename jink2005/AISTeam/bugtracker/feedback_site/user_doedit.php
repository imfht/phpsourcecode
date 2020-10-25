<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_doedit.php,v 1.10 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");

AuthCheckAndLogin();

if (utf8_strlen($_POST['realname']) > 100) {
	ErrorPrintOut("too_long", "realname", "100");
}

if (!trim($_POST['password1'])) {
	ErrorPrintOut("no_empty", "password");
}

if (utf8_strlen($_POST['password1']) > 50) {
	ErrorPrintOut("too_long", "password", "50");
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
	
$sql = "update ".$GLOBALS['BR_customer_user_table']." set realname=".$GLOBALS['connection']->QMagic($_POST['realname']).",
		language=".$GLOBALS['connection']->QMagic($_POST['language'])." where customer_user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_uid']);

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

if ($change_password) {
	$passwd_sql = "update ".$GLOBALS['BR_customer_user_table']." 
		set password=".$GLOBALS['connection']->QMagic(md5($_POST['password1']))." 
		where customer_user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_uid']);
	$GLOBALS['connection']->Execute($passwd_sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

// Unset language to force string_function.php to reload the language setting
unset($_SESSION[SESSION_PREFIX.'language']);

FinishPrintOut("system.php", "finish_update", "user");

?>