<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: schedule_doedit.php,v 1.9 2013/07/05 20:17:48 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/email_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_edit_schedule'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_POST['subject'] == "") {
	ErrorPrintBackFormOut("GET", "schedule_edit.php", 
						  $_POST, "no_empty", "subject");
}

list($year, $month, $day) = explode('-', $_POST['date']);
if (checkdate($_POST['month'], $_POST['day'], $_POST['year']) != 1) {
	ErrorPrintBackFormOut("GET", "schedule_edit.php", $_POST, 
						  "wrong_format", "date");
}
$date = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']));

if ($_POST['schedule_type'] == "project") {
	$project_id = $_POST['project_id'];
} else {
	$project_id = 0;
}

if ($_POST['publish'] == "Y") {
	$_POST['publish'] = "t";
} else {
	$_POST['publish'] = "f";
}

$_POST['subject'] = htmlspecialchars($_POST['subject']);

$to_array = array();
$email_to = explode(",", $_POST['email_to']);
for ($i = 0; $i < sizeof($email_to); $i++) {
	$email_to[$i] = trim($email_to[$i]);
	if ($email_to[$i] != "") {
		array_push($to_array, $email_to[$i]);
	}
}
$to_array = ArrayUnique($to_array);
$to = implode(",", $to_array);

$sql = "select created_by from ".$GLOBALS['BR_schedule_table']." where schedule_id = ".$GLOBALS['connection']->QMagic($_POST['schedule_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$created_by = $result->fields[0];
if (($_SESSION[SESSION_PREFIX.'uid'] != 0) && ($created_by != $_SESSION[SESSION_PREFIX.'uid'])) {
	WriteSyslog("error", "syslog_not_found", "schedule", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "title_schedule");
}

$sql = "update ".$GLOBALS['BR_schedule_table']." set date=".$date.", 
		subject=".$GLOBALS['connection']->QMagic($_POST['subject']).", 
		description=".$GLOBALS['connection']->QMagic($_POST['description']).", 
		project_id = ".$GLOBALS['connection']->QMagic($project_id).", 
		publish=".$GLOBALS['connection']->QMagic($_POST['publish']).", 
		email_to = ".$GLOBALS['connection']->QMagic($to)."
		where schedule_id=".$GLOBALS['connection']->QMagic($_POST['schedule_id']);

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

if ($to != "") {
	LoadingTimerShow();
	SendScheduleEmail($_POST['schedule_id'], $to);
	LoadingTimerHide();
}

FinishPrintOut("schedule.php", "finish_update", "title_schedule", 0);

?>
