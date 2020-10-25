<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_delete.php,v 1.12 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['user_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "user_id");
}
if ($_GET['user_id'] == 0) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "user_id");
}
$sql = "select username from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "user", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "user");
}
$username = $result->fields["username"];

$delete_sql="delete from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_filter_sql="delete from ".$GLOBALS['BR_filter_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_access_sql="delete from ".$GLOBALS['BR_proj_access_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_mailto_sql="delete from ".$GLOBALS['BR_proj_auto_mailto_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);

$update_area_sql = "update ".$GLOBALS['BR_proj_area_table']." set owner=-1 where owner=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_login_log = "delete from ".$GLOBALS['BR_login_log_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_syslog_log = "delete from ".$GLOBALS['BR_syslog_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_schedule_sql = "delete from ".$GLOBALS['BR_schedule_table']." where created_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);

$delete_doc_create_by = "update ".$GLOBALS['BR_document_table']." set created_by=-1 where created_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_doc_history_create_by = "update ".$GLOBALS['BR_document_history_table']." set created_by=-1 where created_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$delete_proj_create_by = "update ".$GLOBALS['BR_project_table']." set created_by=-1 where created_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_filter_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_access_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_mailto_sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->Execute($update_area_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_login_log) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_syslog_log) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_schedule_sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->Execute($delete_doc_create_by) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_doc_history_create_by) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_proj_create_by) or DBError(__FILE__.":".__LINE__);

$project_array = GetAllProjects();
for ($i=0; $i < sizeof($project_array); $i++) {
	$project_id = $project_array[$i]->getprojectid();
	$delete_sql = "update proj".$project_id."_report_table set reported_by=-1 
		where reported_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
	$delete_sql = "update proj".$project_id."_report_table set assign_to=-1 
		where assign_to=".$GLOBALS['connection']->QMagic($_GET['user_id']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
	$delete_sql = "update proj".$project_id."_report_table set fixed_by=-1 
		where fixed_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
	$delete_sql = "update proj".$project_id."_report_table set verified_by=-1 
		where verified_by=".$GLOBALS['connection']->QMagic($_GET['user_id']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
	$delete_sql = "update proj".$project_id."_report_log_table set user_id=-1 
		where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "user", $username);
FinishPrintOut("user_admin.php?user_type=".$_GET['user_type'], 
			   "finish_delete", "user");
      
?>
