<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_doupdate.php,v 1.13 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/project_function.php");
include("include/email_function.php");

AuthCheckAndLogin();

if (!$_POST['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (!$_POST['report_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}

$return_page = "report_update.php?project_id=".$_POST['project_id']."&report_id=".$_POST['report_id'];

if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

if (!trim($_POST['summary'])) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "no_empty", "summary");
}

if (!$_POST['version']) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "no_empty", "version");
}
	
if (utf8_strlen($_POST['version']) > 40) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "too_long", "version", "40");
}

$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];

// 去除html的標籤,讓標籤在網頁中無作用
$_POST['type'] = htmlspecialchars($_POST['type']);
$_POST['version'] = htmlspecialchars($_POST['version']);
$_POST['minor_area'] = htmlspecialchars($_POST['minor_area']);
$_POST['summary'] = htmlspecialchars($_POST['summary']);

$log = "<p>Set Priority:".$STRING[$GLOBALS['priority_array'][$_POST['priority']]].",";
$log .= "Status: ".$GLOBALS['feedback_status'][$_POST['status']];
$log = $log."</p><p>Description:</p><p>".$_POST['description']."</p>";

// 上傳附加檔案資料
if(!$_FILES['file']['tmp_name']) {
	$filename = "";
	if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
		ErrorPrintBackFormOut("GET", $return_page, $_POST,
							  "exceed_max_size", "", ini_get("upload_max_filesize"));
	}
} else {
	if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
		ErrorPrintBackFormOut("GET", $return_page, $_POST, 
							  "wrong_format", "file_upload");
	}

	$org_filename = $_FILES['file']['name'];
	if (utf8_strlen($org_filename) > 252) { /* 100_filename  256-strlen("100_")=252 */
		$subname = strrchr($org_filename, ".");
		if (utf8_strlen($subname) > 251) {
			$filename = utf8_substr($org_filename, 0, 251);
		} else {
			$filename = utf8_substr($org_filename, 0, (251 - utf8_strlen($subname)) ).$subname;
		}
	} else {
		$filename = $org_filename;
	}
		
	$filedata = $GLOBALS['connection']->BlobEncode(fread(fopen($_FILES['file']['tmp_name'], "r"), $_FILES['file']['size']));
}
   
$update_report_sql = "update proj".$_POST['project_id']."_feedback_table set
		summary=".$GLOBALS['connection']->QMagic($_POST['summary']).", 
		type=".$GLOBALS['connection']->QMagic($_POST['type']).", 
		status=".$GLOBALS['connection']->QMagic($_POST['status']).", 
		priority=".$GLOBALS['connection']->QMagic($_POST['priority']).", 
		version=".$GLOBALS['connection']->QMagic($_POST['version']).", 
		reproducibility=".$GLOBALS['connection']->QMagic($_POST['reproducibility'])."
		where report_id=".$GLOBALS['connection']->QMagic($_POST['report_id']);

$now = $GLOBALS['connection']->DBTimeStamp(time());
$new_log_sql = "insert into proj".$_POST['project_id']."_feedback_content_table (
        report_id, customer_email, post_time, description, filename, filedata) values(
		".$GLOBALS['connection']->QMagic($_POST['report_id']).", 
		".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_email']).", $now, 
		".$GLOBALS['connection']->QMagic($log).", 
		".$GLOBALS['connection']->QMagic($filename).", 
		".$GLOBALS['connection']->QMagic($filedata).");";

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($update_report_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($new_log_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->CompleteTrans();

LoadingTimerShow();
SendReportEmail($_POST['project_id'], $_POST['report_id'], $_SESSION[SESSION_PREFIX.'feedback_email']);
LoadingTimerHide();

FinishPrintOut("project_list.php?project_id=".$_POST['project_id'], "finish_update", "report", 0);

?>
