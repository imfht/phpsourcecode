<?php 
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_donew.php,v 1.12 2013/06/29 20:18:15 alex Exp $
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

if (!isset($_POST['project_id']) || ($_POST['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

// Get project data
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
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
	ErrorPrintBackFormOut("GET", "faq_new.php", $_POST, 
						  "no_empty", "question");
}

if ($_POST['is_verified'] != 't') {
	$_POST['is_verified'] = 'f';
	$verified_by = "null";
	$verified_date = "null";
} else {
	$verified_by = $_SESSION[SESSION_PREFIX.'uid'];
	$verified_date = $GLOBALS['connection']->DBTimeStamp(time());
	$_POST['assign_to'] = "-1";
}


$GLOBALS['connection']->StartTrans();

$now = $GLOBALS['connection']->DBTimeStamp(time());
$sql = "insert into ".$GLOBALS['BR_faq_content_table']."(question, answer,
		project_id, created_by, created_date, last_update, is_verified, assign_to, verified_by,
		verified_date) values(".$GLOBALS['connection']->QMagic($_POST['question']).", 
		".$GLOBALS['connection']->QMagic($_POST['answer']).", 
		".$GLOBALS['connection']->QMagic($_POST['project_id']).", ".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).",
		$now, $now, ".$GLOBALS['connection']->QMagic($_POST['is_verified']).", ".$_POST['assign_to'].",
		$verified_by, $verified_date)";

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$faq_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_faq_content_table'], 'faq_id');

$all_belong_class = explode(",", $_POST['belong_class']);

for ($i=0; $i < sizeof($all_belong_class); $i++) {
	if (!is_numeric($all_belong_class[$i])) {
		continue;
	}
	$sql = "insert into ".$GLOBALS['BR_faq_map_table']."(faq_id, faq_class_id) 
			values(".$GLOBALS['connection']->QMagic($faq_id).", ".$GLOBALS['connection']->QMagic($all_belong_class[$i]).")";
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_new_xxx", "faq", "".$project_name." FAQ id:".$faq_id);

LoadingTimerShow();
SendFAQEmail($faq_id, $_SESSION[SESSION_PREFIX.'uid']);
LoadingTimerHide();

FinishPrintOut("faq_admin.php?project_id=".$_POST['project_id'], "finish_new", "faq", 0);

?>
