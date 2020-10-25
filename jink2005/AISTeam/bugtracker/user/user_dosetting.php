<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_dosetting.php,v 1.8 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!isset($_POST['user_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "user_id");
}

if ($_SESSION[SESSION_PREFIX.'uid'] != $_POST['user_id']) {
	if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_privilege");
	}
}

// 將 $perpage 轉成整數，如果輸入 234ba，則 $perpage=234；如果是 ab123，則 $perpage=0
$_POST['perpage'] = intval($_POST['perpage']);
if ($_POST['perpage'] > 32767) {
	ErrorPrintOut("too_long", "report_per_page", "32767");
}

if ($_POST['perpage'] == 0) {$_POST['perpage'] = 100;}
if ($_POST['show_shared_filter'] == "Y") {
	$_POST['show_shared_filter'] = "t";
} else {
	$_POST['show_shared_filter'] = "f";
}
if ($_POST['show_in_blank'] == "Y") {
	$_POST['show_in_blank'] = "t";
} else {
	$_POST['show_in_blank'] = "f";
}

$setting_sql = "update ".$GLOBALS['BR_user_table']." set 
	perpage=".$GLOBALS['connection']->QMagic($_POST['perpage']).", 
	default_filter=".$GLOBALS['connection']->QMagic($_POST['default_filter']).",";
$setting_sql .= "show_shared_filter=".$GLOBALS['connection']->QMagic($_POST['show_shared_filter']).", 
	show_in_blank=".$GLOBALS['connection']->QMagic($_POST['show_in_blank']).",";

for ($i = 0; $i<sizeof($show_column_array); $i++) {

	$show_column = "show_".$show_column_array[$i];
	if ($_POST[$show_column] != 'Y') {
		$_POST[$show_column]='f';
	} else {
		$_POST[$show_column]='t';
	}
	$setting_sql = $setting_sql.$show_column."=".$GLOBALS['connection']->QMagic($_POST[$show_column]).",";

}

$setting_sql=$setting_sql." language=".$GLOBALS['connection']->QMagic($_POST['language'])." where user_id=".$GLOBALS['connection']->QMagic($_POST['user_id']);

$result = $GLOBALS['connection']->Execute($setting_sql) or DBError(__FILE__.":".__LINE__);

// unset the $_SESSION[SESSION_PREFIX.'language'] to force string.php to reload language
unset($_SESSION[SESSION_PREFIX.'language']);

FinishPrintOut("../system/system.php", "finish_update", "user");
?>
