<?php 
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_doedit.php,v 1.12 2013/06/29 20:18:15 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/email_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_faq'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_POST['faq_id']) || ($_POST['faq_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "faq");
}

// Get FAQ Content
$sql = "select * from ".$GLOBALS['BR_faq_content_table']."  where faq_id=".$GLOBALS['connection']->QMagic($_POST['faq_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "faq");
}

$project_id = $result->fields["project_id"];
$org_is_verified = $result->fields["is_verified"];

// Get project data
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$project_line = $project_result->Recordcount();
if ($project_line == 1) {
	$project_name = $project_result->fields["project_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$_POST['question'] = trim($_POST['question']);
if (!$_POST['question']) {
	ErrorPrintBackFormOut("GET", "faq_edit.php", $_POST, 
						  "no_empty", "question");
}

$now = $GLOBALS['connection']->DBTimeStamp(time());
if ($_POST['is_verified'] != 't') {
	$_POST['is_verified'] = 'f';
	$verified_by = "null";
	$verified_date = "null";
	
	$sql = "update ".$GLOBALS['BR_faq_content_table']." set question=".$GLOBALS['connection']->QMagic($_POST['question']).",
			answer=".$GLOBALS['connection']->QMagic($_POST['answer']).", last_update=$now, is_verified='f', verified_by=null,
			verified_date=null, assign_to=".$GLOBALS['connection']->QMagic($_POST['assign_to'])." 
			where faq_id=".$GLOBALS['connection']->QMagic($_POST['faq_id']);
} else {
	if ($org_is_verified != 't') {
		$sql = "update ".$GLOBALS['BR_faq_content_table']." set 
			question=".$GLOBALS['connection']->QMagic($_POST['question']).", 
			answer=".$GLOBALS['connection']->QMagic($_POST['answer']).",
			last_update=$now, is_verified='t', verified_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).",
			verified_date=$now, assign_to='-1' 
			where faq_id=".$GLOBALS['connection']->QMagic($_POST['faq_id']);
	} else {
		$sql = "update ".$GLOBALS['BR_faq_content_table']." set 
			question=".$GLOBALS['connection']->QMagic($_POST['question']).", answer=".$GLOBALS['connection']->QMagic($_POST['answer']).", 
			last_update=$now, 
			assign_to='-1' where faq_id=".$GLOBALS['connection']->QMagic($_POST['faq_id']);
	}
}

$GLOBALS['connection']->StartTrans();

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

// Remove old category
$sql = "delete from ".$GLOBALS['BR_faq_map_table']." where faq_id=".$GLOBALS['connection']->QMagic($_POST['faq_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$all_belong_class = explode(",", $_POST['belong_class']);

for ($i=0; $i < sizeof($all_belong_class); $i++) {
	if (!is_numeric($all_belong_class[$i])) {
		continue;
	}
	$sql = "insert into ".$GLOBALS['BR_faq_map_table']."(faq_id, faq_class_id) 
			values(".$GLOBALS['connection']->QMagic($_POST['faq_id']).", ".$GLOBALS['connection']->QMagic($all_belong_class[$i]).")";
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_edit_xxx", "faq", "".$project_name." FAQ id:".$_POST['faq_id']);

LoadingTimerShow();
SendFAQEmail($_POST['faq_id'], $_SESSION[SESSION_PREFIX.'uid']);
LoadingTimerHide();

FinishPrintOut("faq_admin.php?project_id=".$project_id, "finish_update", "faq", 0);

?>
