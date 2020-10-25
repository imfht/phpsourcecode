<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: schedule_delete.php,v 1.7 2013/07/05 20:17:48 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_edit_schedule'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!(isset($_GET['schedule_id'])) ){
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "schedule_id");
}

$sql = "select created_by from ".$GLOBALS['BR_schedule_table']." where schedule_id = ".$GLOBALS['connection']->QMagic($_GET['schedule_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$created_by = $result->fields[0];
if (($_SESSION[SESSION_PREFIX.'uid'] != 0) && ($created_by != $_SESSION[SESSION_PREFIX.'uid'])) {
	WriteSyslog("error", "syslog_not_found", "schedule", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "title_schedule");
}

$sql = "delete from ".$GLOBALS['BR_schedule_table']." where schedule_id=".$GLOBALS['connection']->QMagic($_GET['schedule_id']);

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
				
FinishPrintOut("schedule.php", "finish_delete", "title_schedule");
?>