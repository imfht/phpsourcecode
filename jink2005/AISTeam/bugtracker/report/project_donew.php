<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_donew.php,v 1.16 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_project'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!trim($_POST['project_name'])) {
	ErrorPrintBackFormOut("GET", "project_new.php", $_POST, 
						  "no_empty", "project_name");
}
if (utf8_strlen($_POST['project_name']) > 100) {
	ErrorPrintBackFormOut("GET", "project_new.php", $_POST, 
						  "too_long", "project_name", "100");
	
}
if (utf8_strlen($_POST['version_pattern']) > 40) {
	ErrorPrintBackFormOut("GET", "project_new.php", $_POST, 
						  "too_long", "version_pattern", "40");
	
}

// Check whether the project name is unique.
$check_sql = "select * from ".$GLOBALS['BR_project_table']." 
	where project_name=".$GLOBALS['connection']->QMagic($_POST['project_name']);
$check_result = $GLOBALS['connection']->Execute($check_sql) or DBError(__FILE__.":".__LINE__);
$line = $check_result->Recordcount();
if ($line != 0) {
	ErrorPrintBackFormOut("GET", "project_new.php", $_POST, 
						  "have_same", "project_name", $_POST['project_name']);
}

$GLOBALS['connection']->StartTrans();

// 新增程式資料到 project 中
$now = $GLOBALS['connection']->DBTimeStamp(time());
$sql = "insert into ".$GLOBALS[BR_project_table]."(project_name, created_date, created_by, version_pattern)
	values(".$GLOBALS['connection']->QMagic($_POST['project_name']).", $now, 
		".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).", 
		".$GLOBALS['connection']->QMagic($_POST['version_pattern']).")";
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$project_id = $GLOBALS['connection']->Insert_ID($GLOBALS[BR_project_table], 'project_id');

$SqlFile = $GLOBALS["SYS_PROJECT_PATH"]."/setup/sql/project.".$GLOBALS['BR_dbtype'];
$fd = fopen($SqlFile, "r");
if (!$fd) {
	DBError(__FILE__.":".__LINE__);
}
$sql = fread($fd, filesize($SqlFile));
fclose($fd);
$sql_array = explode(";", $sql);
for ($i = 0; $i < sizeof($sql_array) - 1; $i++) {
	$sql_array[$i] = str_replace("\n", "", $sql_array[$i]);
	$sql_array[$i] = str_replace("@PROJECT_ID@", "$project_id", $sql_array[$i]);
	$GLOBALS['connection']->Execute($sql_array[$i]) or DBError(__FILE__.":".__LINE__);
}

$auto_mail_array = array();
for ($i=0; $i<6; $i++) {
	$this_arg = "auto_email_".$i;
	if ($_POST[$this_arg] != -1) {
		if (IsInArray($auto_mail_array, $_POST[$this_arg]) == -1) {
			array_push($auto_mail_array, $_POST[$this_arg]);
		}
	}
}

for ($i = 0; $i < sizeof($auto_mail_array); $i++) {
	$auto_email_sql = "insert into ".$GLOBALS['BR_proj_auto_mailto_table']."(project_id, user_id, can_unsubscribe) 
		values(".$GLOBALS['connection']->QMagic($project_id).", ".$GLOBALS['connection']->QMagic($auto_mail_array[$i]).", 'f')";
	$GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
}

$feedback_mailto_array = array();
for ($i=0; $i<6; $i++) {
	$this_arg = "feedback_mailto_".$i;
	if ($_POST[$this_arg] != -1) {
		if (IsInArray($feedback_mailto_array, $_POST[$this_arg]) == -1) {
			array_push($feedback_mailto_array, $_POST[$this_arg]);
		}
	}
}

for ($i = 0; $i < sizeof($feedback_mailto_array); $i++) {
	$feedback_mailto_sql = "insert into ".$GLOBALS['BR_proj_feedback_mailto_table']."(project_id, user_id) 
		values(".$GLOBALS['connection']->QMagic($project_id).", ".$GLOBALS['connection']->QMagic($feedback_mailto_array[$i]).")";
	$GLOBALS['connection']->Execute($feedback_mailto_sql) or DBError(__FILE__.":".__LINE__);
}

$all_allow_uid = explode(",", $_POST['allow_uid']);

$now = $GLOBALS['connection']->DBTimeStamp(time());
for ($i=0; $i < sizeof($all_allow_uid); $i++) {
	if (!is_numeric($all_allow_uid[$i])) {
		continue;
	}
	$list_sql="insert into ".$GLOBALS['BR_proj_access_table']."(user_id, project_id, last_read) 
				values(".$GLOBALS['connection']->QMagic($all_allow_uid[$i]).", ".$GLOBALS['connection']->QMagic($project_id).", $now)";
	$GLOBALS['connection']->Execute($list_sql) or DBError(__FILE__.":".__LINE__);
}

if ($GLOBALS['SYS_FILE_IN_DB'] != 1) {
	$newdir="upload/project".$project_id;
	mkdir($newdir,0700) or DBError(__FILE__.":".__LINE__);
}

// end of transaction
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_new_xxx", "project", $_POST['project_name']);
FinishPrintOut("../index.php", "finish_new", "project");
?>
