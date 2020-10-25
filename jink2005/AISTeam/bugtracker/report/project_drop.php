<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_drop.php,v 1.14 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_delete_report'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

$sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "project_id", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project_id");
}
$project_name = $result->fields["project_name"];

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$delete_project_sql="delete from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$drop_report_table_sql="drop table proj".$_GET['project_id']."_report_table CASCADE";
$drop_report_log_sql="drop table proj".$_GET['project_id']."_report_log_table";
$drop_area_sql="delete from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$drop_seealso_sql="drop table proj".$_GET['project_id']."_seealso_table";		
$drop_feedback_sql = "drop table proj".$_GET['project_id']."_feedback_table";
$drop_feedback_content_sql = "drop table proj".$_GET['project_id']."_feedback_content_table";
$drop_feedback_map_sql = "drop table proj".$_GET['project_id']."_feedback_map_table";
$delete_access_sql = "delete from ".$GLOBALS['BR_proj_access_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$delete_customer_access_sql = "delete from ".$GLOBALS['BR_proj_customer_access_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$delete_mailto_sql = "delete from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$delete_schedule_sql = "delete from ".$GLOBALS['BR_schedule_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$delete_feedback_mailto_sql = "delete from ".$GLOBALS['BR_proj_feedback_mailto_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);

// 使用 transaction 來確保所有動作都完成，或是所有動作都不完成
$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($delete_project_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_report_table_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_report_log_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_area_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_seealso_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_feedback_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_feedback_content_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_feedback_map_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_access_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_customer_access_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_mailto_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_schedule_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_feedback_mailto_sql) or DBError(__FILE__.":".__LINE__);

if ($GLOBALS['SYS_FILE_IN_DB'] != 1) {
	$delete_dir="upload/project".$_GET['project_id'];
	exec("rm -rf $delete_dir") and DBError(__FILE__.":".__LINE__);
}

// 刪除所有的其他程式中的 also see
$get_all_proj = "select project_id from ".$GLOBALS["BR_project_table"];
$get_all_proj_result = $GLOBALS['connection']->Execute($get_all_proj) or
			DBError(__FILE__.":".__LINE__);

while ($row = $get_all_proj_result->FetchRow()){
	$project_id = $row["project_id"];
	$delete_alsosee_sql="delete from proj".$project_id."_seealso_table where see_also_project=".$GLOBALS['connection']->QMagic($_GET['project_id']);
	$GLOBALS['connection']->Execute($delete_alsosee_sql) or DBError(__FILE__.":".__LINE__);
}

// Delete FAQ
$get_all_class = "select faq_class_id from ".$GLOBALS['BR_faq_class_table'];
$get_all_class_result = $GLOBALS['connection']->Execute($get_all_class) or
					DBError(__FILE__.":".__LINE__);

while ($row = $get_all_class_result->FetchRow()) {
	$faq_class_id = $row["faq_class_id"];
	$delete_class_sql = "delete from ".$GLOBALS['BR_faq_map_table']." 
			where faq_class_id=".$GLOBALS['connection']->QMagic($faq_class_id);
	$GLOBALS['connection']->Execute($delete_class_sql) or DBError(__FILE__.":".__LINE__);
}
$drop_faq_class_sql = "delete from ".$GLOBALS['BR_faq_class_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$drop_faq_content_sql = "delete from ".$GLOBALS['BR_faq_content_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$GLOBALS['connection']->Execute($drop_faq_class_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($drop_faq_content_sql) or DBError(__FILE__.":".__LINE__);

// Delete labels
$sql = "select label_id from ".$GLOBALS["BR_label_table"]." WHERE project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
while ($row = $result->FetchRow()){
	$label_id = $row["label_id"];
	$sql = "DELETE FROM ".$GLOBALS["BR_label_mapping_table"]." WHERE label_id=".$GLOBALS['connection']->QMagic($label_id);
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}
$sql = "DELETE FROM ".$GLOBALS["BR_label_table"]." WHERE project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);


$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "project", $project_name);
FinishPrintOut("../index.php", "finish_delete", "project");

?>
