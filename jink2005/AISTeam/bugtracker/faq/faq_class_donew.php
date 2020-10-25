<?php 
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_class_donew.php,v 1.10 2013/06/29 20:18:15 alex Exp $
 *
 */
include("../include/header.php");

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

$_POST['class_name'] = trim($_POST['class_name']);
if (!$_POST['class_name']) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=".$_POST['project_id'], $_POST, 
						  "no_empty", "class_name");
}

if (utf8_strlen($_POST['class_name']) > 50) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=".$_POST['project_id'], $_POST,
						  "too_long", "class_name", "50");
}

$pattern = str_replace('\\', '\\\\', $reserve_words);
if (preg_match("/[".$pattern."]/", $_POST['class_name'])) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=".$_POST['project_id'], $_POST, 
						  "reserve_hint", "class_name", $reserve_words);
}

// Check category name
$check_sql = "select * from ".$GLOBALS['BR_faq_class_table']." 
			where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id'])." and class_name=".$GLOBALS['connection']->QMagic($_POST['class_name']);

$check_result = $GLOBALS['connection']->Execute($check_sql) or  
			DBError(__FILE__.":".__LINE__);

$line = $check_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=".$_POST['project_id'], $_POST,
						  "have_same", "class_name", $_POST['class_name']);
}

$sql = "insert into ".$GLOBALS['BR_faq_class_table']."(class_name, project_id)
		values(".$GLOBALS['connection']->QMagic($_POST['class_name']).", ".$GLOBALS['connection']->QMagic($_POST['project_id']).")";

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

WriteSyslog("info", "syslog_new_xxx", "faq_class", $_POST['class_name']);
FinishPrintOut("faq_class.php?project_id=".$_POST['project_id'], "finish_new", "faq_class");

?>
