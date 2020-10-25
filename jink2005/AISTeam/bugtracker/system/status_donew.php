<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: status_donew.php,v 1.7 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_status'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!trim($_POST['status_name'])) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST, 
						  "no_empty", "status_name");
}
if (!trim($_POST['status_color'])) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST, 
						  "no_empty", "color");
}
if (!trim($_POST['status_type'])) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST, 
						  "no_empty", "type");
}

if (utf8_strlen($_POST['status_name']) > 60) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST,
						  "too_long", "status_name", "60");
}
if (utf8_strlen($_POST['status_color']) > 20) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST,
						  "too_long", "color", "20");
}
if (($_POST['status_type'] != "active") && ($_POST['status_type'] != "closed")) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST,
						  "wrong_format", "type");
}
	
$check_sql = "select * from ".$GLOBALS['BR_status_table']." 
				where status_name=".$GLOBALS['connection']->QMagic($_POST['status_name']);

$check_result = $GLOBALS['connection']->Execute($check_sql) or 
		DBError(__FILE__.":".__LINE__);

$line = $check_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "status_new.php", $_POST,
						  "have_same", "status_name", $_POST['status_name']);
}

$sql = "insert into ".$GLOBALS['BR_status_table']."(status_name, status_color,
		status_type)
		values(".$GLOBALS['connection']->QMagic($_POST['status_name']).", 
		".$GLOBALS['connection']->QMagic($_POST['status_color']).",
		".$GLOBALS['connection']->QMagic($_POST['status_type']).")";

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

WriteSyslog("info", "syslog_new_xxx", "status", $_POST['status_name']);
FinishPrintOut("status_admin.php", "finish_new", "status");

?>
