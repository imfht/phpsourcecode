<?php 
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_class_doedit.php,v 1.9 2013/06/29 20:18:15 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_faq'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_POST['faq_class_id']) || ($_POST['faq_class_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "faq_class_id");
}
// Get project data
$faq_class_sql = "select * from ".$GLOBALS['BR_faq_class_table']." where faq_class_id=".$GLOBALS['connection']->QMagic($_POST['faq_class_id']);
$faq_class_result = $GLOBALS['connection']->Execute($faq_class_sql) or DBError(__FILE__.":".__LINE__);
$line = $faq_class_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "faq_class", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "faq_class");
} else {
	$project_id = $faq_class_result->fields["project_id"];
}

$_POST['class_name'] = trim($_POST['class_name']);
if (!$_POST['class_name']) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=$project_id", $_POST, 
						  "no_empty", "class_name");
}

if (utf8_strlen($_POST['class_name']) > 50) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=$project_id", $_POST,
						  "too_long", "class_name", "50");
}

$pattern = str_replace('\\', '\\\\', $reserve_words);
if (preg_match("/[".$pattern."]/", $_POST['class_name'])) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=$project_id", $_POST, 
						  "reserve_hint", "class_name", $reserve_words);
}

// Check category name
$check_sql = "select * from ".$GLOBALS['BR_faq_class_table']." 
			where project_id=".$GLOBALS['connection']->QMagic($project_id)." 
				and class_name=".$GLOBALS['connection']->QMagic($_POST['class_name'])." 
				and faq_class_id!=".$GLOBALS['connection']->QMagic($_POST['faq_class_id']);

$check_result = $GLOBALS['connection']->Execute($check_sql) or  
			DBError(__FILE__.":".__LINE__);

$line = $check_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "faq_class.php?project_id=$project_id", $_POST,
						  "have_same", "class_name", $_POST['class_name']);
}

$sql = "update ".$GLOBALS['BR_faq_class_table']." set 
		class_name=".$GLOBALS['connection']->QMagic($_POST['class_name'])." where faq_class_id=".$GLOBALS['connection']->QMagic($_POST['faq_class_id']);

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

WriteSyslog("info", "syslog_edit_xxx", "faq_class", $_POST['class_name']);
FinishPrintOut("faq_class.php?project_id=".$project_id, "finish_update", "faq_class");

?>
