<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: status_delete.php,v 1.10 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_status'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!isset($_GET['status_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "status_id");
}
$sql = "select status_name from ".$GLOBALS['BR_status_table']." where status_id=".$GLOBALS['connection']->QMagic($_GET['status_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "status", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "status");
}
$status_name = $result->fields["status_name"];

$GLOBALS['connection']->StartTrans();
	
$delete_sql="delete from ".$GLOBALS['BR_status_table']." where status_id=".$GLOBALS['connection']->QMagic($_GET['status_id']);

$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$project_array = GetAllProjects();
for ($i = 0; $i<sizeof($project_array); $i++) {
	$sql = "update proj".$project_array[$i]->getprojectid()."_report_table set
			status=null where status=".$GLOBALS['connection']->QMagic($_GET['status_id']);
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

$sql = "delete from ".$GLOBALS['BR_group_allow_status_table']." where status_id=".$GLOBALS['connection']->QMagic($_GET['status_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "status", $status_name);
FinishPrintOut("status_admin.php", "finish_delete", "status");
      
?>
