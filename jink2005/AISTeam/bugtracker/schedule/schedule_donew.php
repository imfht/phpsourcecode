<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: schedule_donew.php,v 1.11 2013/07/05 20:17:48 alex Exp $
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
	ErrorPrintBackFormOut("GET", "schedule_new.php", 
						  $_POST, "no_empty", "subject");
}

$date = $GLOBALS['connection']->DBTimeStamp($_POST['time']);

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

$sql = "insert into ".$GLOBALS['BR_schedule_table']."(date, created_by, 
		subject, description, project_id, publish, email_to)
		values($date, ".$_SESSION[SESSION_PREFIX.'uid'].", 
		".$GLOBALS['connection']->QMagic($_POST['subject']).", 
		".$GLOBALS['connection']->QMagic($_POST['description']).", 
		".$GLOBALS['connection']->QMagic($project_id).", 
		".$GLOBALS['connection']->QMagic($_POST['publish']).", 
		".$GLOBALS['connection']->QMagic($to).")";

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$schedule_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_schedule_table'], 'schedule_id');

if ($to != "") {
	LoadingTimerShow();
	SendScheduleEmail($schedule_id, $to);
	LoadingTimerHide();
}

FinishPrintOut("schedule.php", "finish_new", "title_schedule", 0);

?>
