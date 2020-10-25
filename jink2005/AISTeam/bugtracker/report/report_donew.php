<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_donew.php,v 1.23 2013/07/05 22:41:04 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/user_function.php");
include("../include/status_function.php");
include("../include/customer_function.php");
include("../include/report_function.php");
include("../include/email_function.php");
include("../include/feedback_email_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_report'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!$_POST['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
if ($_POST['feedback_report_id']) {
	$return_page = $_POST['back_from']."?project_id=".$_POST['project_id']."&report_id=".$_POST['report_id'];
	
	$feedback_system_sql = "select feedback_system_name, import_description from ".$GLOBALS['BR_feedback_config_table'];
	$feedback_system_result = $GLOBALS['connection']->Execute($feedback_system_sql) or DBError(__FILE__.":".__LINE__);
	$line = $feedback_system_result->Recordcount();
	if ($line != 1) {
		DBError(__FILE__.":".__LINE__);
	}

	if ($_POST['feedback_description'] == "") {
		$import_description = $feedback_system_result->fields["import_description"];
	} else {
		$import_description = $_POST['feedback_description'];
	}
	
	$feedback_system_name = $feedback_system_result->fields["feedback_system_name"];
} else {
	$return_page = "report_new.php?project_id=".$_POST['project_id'];
}
if (!trim($_POST['summary'])) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "no_empty", "summary");
}
if (!$_POST['status']) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "no_empty", "status");
}
if (utf8_strlen($_POST['area']) > 40) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "too_long", "area", "40");
}
if (utf8_strlen($_POST['minor_area']) > 40) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "too_long", "minor_area", "40");
}

$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];
$version_pattern = $project_result->fields["version_pattern"];

// 處理 version
if (!isset($_POST['version'])) {
	$version = "";
	$version_changed = 0;
	for ($i = 0; $i <= strlen($version_pattern); $i++) {
		if (($version_pattern{$i} == '%') || ($version_pattern{$i} == '@')) {
			if ($_POST['version'.$i] != -1) {
				$version .= $_POST['version'.$i];
				$version_changed = 1;
			}
		} else {
			$version .= $version_pattern{$i};
		}
	}
	if ($version_changed == 0) {
		$version = "";
	}
} else {
	$version = $_POST['version'];
}

if (utf8_strlen($version) > 40) {
	ErrorPrintBackFormOut("GET", $return_page, $_POST, 
						  "too_long", "version", "40");
}

$userarray = GetAllUsers(1, 1);
$status_array = GetStatusArray();


// 取得負責該 Area 及 minor area 的 owner
$owner_sql = "select owner from ".$GLOBALS['BR_proj_area_table']." 
	where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id'])." and area_name=".$GLOBALS['connection']->QMagic($_POST['area']);
$owner_result = $GLOBALS['connection']->Execute($owner_sql) or DBError(__FILE__.":".__LINE__);
$line = $owner_result->Recordcount();
if ($line == 1) {
	$owner = $owner_result->fields["owner"];
} else {
	$owner = -1;
}
$minor_owner_sql = "select owner from ".$GLOBALS['BR_proj_area_table']." 
		where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id'])." and area_name=".$GLOBALS['connection']->QMagic($_POST['minor_area']);
$minor_owner_result = $GLOBALS['connection']->Execute($minor_owner_sql) or 
				DBError(__FILE__.":".__LINE__);
$line = $minor_owner_result->Recordcount();
if ($line == 1) {
	$minor_owner = $minor_owner_result->fields["owner"];
} else {
	$minor_owner = -1;
}

// If there is no assing_to, set the assign_to to minor_director，if no minior director,
// set to area director
if ($_POST['assign_to'] == -1) {
	if ($minor_owner) {
		$_POST['assign_to'] = $minor_owner;
	} elseif ($owner) {
		$_POST['assign_to'] = $owner;
	}
}


// Disable HTML tags
$_POST['type'] = htmlspecialchars($_POST['type']);
$_POST['version'] = htmlspecialchars($_POST['version']);
$_POST['area'] = htmlspecialchars($_POST['area']);
$_POST['minor_area'] = htmlspecialchars($_POST['minor_area']);
$_POST['summary'] = htmlspecialchars($_POST['summary']);

$statusclass = GetStatusClassByID($status_array, $_POST['status']);

if ($_POST['feedback_report_id']) {
	$log = "<p>Import from $feedback_system_name</p>";
} else {
	$log = "";
}
$log .= "<p>Set Priority:".$STRING[$GLOBALS['priority_array'][$_POST['priority']]].", 
	Status:".htmlspecialchars($statusclass->getstatusname()).", Assign To:";
if ($_POST['assign_to']) {$log = $log.UidToUsername($userarray, $_POST['assign_to']);}
$log=$log."</p><p>Description:</p><p>".$_POST['description']."</p>";

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
	
	if ($GLOBALS['SYS_FILE_IN_DB'] == 1) {
		$filedata = $GLOBALS['connection']->BlobEncode(fread(fopen($_FILES['file']['tmp_name'], "r"), $_FILES['file']['size']));
	} else {
		$org_filename = $filename;
		$dest_file = "upload/project".$_POST['project_id']."/".$filename;

		$num=1;
		while (file_exists($dest_file)) {
			
			if ($num > 100) {
				$subname = strrchr($filename, ".");
				if (utf8_strlen($subname) > 250) {
					$filename = date("U");
					$dest_file = "upload/project".$_POST['project_id']."/".$filename;
				} else {
					$filename = date("U").$subname;
					$dest_file = "upload/project".$_POST['project_id']."/".$filename;
				}				
			} else {
				$filename = $num."_".$org_filename;
				$dest_file = "upload/project".$_POST['project_id']."/".$filename;
			}
			$num++; // if previous file name existed then thy another number+_+filename
		}
		move_uploaded_file($_FILES['file']['tmp_name'], $dest_file );
	} // End of file in db
}
if ($_POST['reported_by_customer'] == "") {
	$_POST['reported_by_customer'] = "null";
}

$now = $GLOBALS['connection']->DBTimeStamp(time());

$new_report_sql = "insert into proj".$_POST['project_id']."_report_table( 
	summary, reported_by, created_date, priority, status, version, assign_to,
	area, minor_area, last_update, type, reproducibility, fixed_by, verified_by,
	reported_by_customer) values(
	".$GLOBALS['connection']->QMagic($_POST['summary']).", 
	".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).", 
	$now,
	".$GLOBALS['connection']->QMagic($_POST['priority']).",
	".$GLOBALS['connection']->QMagic($_POST['status']).", 
	".$GLOBALS['connection']->QMagic($version).",
	".$GLOBALS['connection']->QMagic($_POST['assign_to']).",
	".$GLOBALS['connection']->QMagic($_POST['area']).",
	".$GLOBALS['connection']->QMagic($_POST['minor_area']).", 
	$now, 
	".$GLOBALS['connection']->QMagic($_POST['type']).",
	".$GLOBALS['connection']->QMagic($_POST['reproducibility']).", -1, -1, ".$_POST['reported_by_customer'].")";


$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($new_report_sql) or DBError(__FILE__.":".__LINE__);
$report_id = $GLOBALS['connection']->Insert_ID("proj".$_POST['project_id']."_report_table", 'report_id');

$new_log_sql = "insert into proj".$_POST['project_id']."_report_log_table (
	report_id, user_id, post_time, description, filename, filedata) values(
	$report_id, ".$_SESSION[SESSION_PREFIX.'uid'].", $now, 
	".$GLOBALS['connection']->QMagic($log).", 
	".$GLOBALS['connection']->QMagic($filename).", 
	".$GLOBALS['connection']->QMagic($filedata).");";

$GLOBALS['connection']->Execute($new_log_sql) or DBError(__FILE__.":".__LINE__);

if ($_POST['feedback_report_id']) {

	$description = "<p>Set Status: In Process</p>";
	$description .= "<p>Description:</p><p>".$import_description."</p>";

	// Set the status to "In process"
	$feedback_sql = "update proj".$_POST['project_id']."_feedback_table set
			status=2 where report_id=".$GLOBALS['connection']->QMagic($_POST['feedback_report_id']);
	$GLOBALS['connection']->Execute($feedback_sql) or DBError(__FILE__.":".__LINE__);
	
	// Insert a new log in feedback system
	$new_log_sql = "insert into proj".$_POST['project_id']."_feedback_content_table (
			report_id, internal_user_id, post_time, description) values(
			".$GLOBALS['connection']->QMagic($_POST['feedback_report_id']).", 
			".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).", $now, 
			".$GLOBALS['connection']->QMagic($description).");";
	$GLOBALS['connection']->Execute($new_log_sql) or DBError(__FILE__.":".__LINE__);

	// Link the feedback system and internal system
	$query_map = "select * from proj".$_POST['project_id']."_feedback_map_table
			where feedback_report_id=".$GLOBALS['connection']->QMagic($_POST['feedback_report_id'])." and 
			internal_report_id=".$report_id;
	$map_result = $GLOBALS['connection']->Execute($query_map) or DBError(__FILE__.":".__LINE__);
	if ($map_result->Recordcount() == 0) {
		$map_sql = "insert into proj".$_POST['project_id']."_feedback_map_table(
			feedback_report_id, internal_report_id) values(".$GLOBALS['connection']->QMagic($_POST['feedback_report_id']).",
			".$report_id.");";
		$GLOBALS['connection']->Execute($map_sql) or DBError(__FILE__.":".__LINE__);
	}
}

$GLOBALS['connection']->CompleteTrans();

LoadingTimerShow();
SendReportEmail($_POST['project_id'], $report_id);
LoadingTimerHide();

if (!$_POST['feedback_report_id']) {
	FinishPrintOut("project_list.php?project_id=".$_POST['project_id'], "finish_new", "report", 0);
} else {
	FinishPrintOut($_POST['finish_from']."?project_id=".$_POST['project_id']."&report_id=".$_POST['feedback_report_id'], "finish_import", "report", 0);
	SendFeedbackReportEmail($_POST['project_id'], $_POST['feedback_report_id'], $_SESSION[SESSION_PREFIX.'uid']);
}

?>

