<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: syslog_clear.php,v 1.7 2008/11/28 10:36:31 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");

AuthCheckAndLogin();

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_GET['feedback']) {
	$sql = "delete from ".$GLOBALS['BR_feedback_syslog_table'];
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	WriteSyslog("info", "clear_feedback_syslog");
	FinishPrintOut("syslog.php?feedback=1", "finish_delete", "feedback_syslog");
} else {
	$sql = "delete from ".$GLOBALS['BR_syslog_table'];
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	WriteSyslog("info", "clear_syslog");
	FinishPrintOut("syslog.php", "finish_delete", "syslog");
}

include("../include/tail.php");
?>
