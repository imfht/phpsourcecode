<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_report_delete.php,v 1.10 2013/07/07 21:25:52 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_feedback'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (!isset($_GET['report_id']) || ($_GET['report_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$sql = "select summary from proj".$_GET['project_id']."_feedback_table where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}
$summary =$result->fields["summary"];

$GLOBALS['connection']->StartTrans();

$map_sql = "delete from proj".$_GET['project_id']."_feedback_map_table 
			 where feedback_report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
$report_delete_sql = "delete from proj".$_GET['project_id']."_feedback_table where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
$content_delete_sql = "delete from proj".$_GET['project_id']."_feedback_content_table where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
         
$GLOBALS['connection']->Execute($map_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($report_delete_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($content_delete_sql) or DBError(__FILE__.":".__LINE__);
         
$GLOBALS['connection']->CompleteTrans();
         
WriteSyslog("info", "syslog_delete_xxx", "title_feedback", $summary);

$extra_params = GetExtraParams($_GET, "search_key,customer_filter,page,sort_by,sort_method");

FinishPrintOut("feedback_list.php?project_id=".$_GET['project_id'].$extra_params, "finish_delete", "title_feedback");
?>