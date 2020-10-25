<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_copyto.php,v 1.8 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!$_POST['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_report'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
if (CheckProjectAccessable($_POST['copyto_project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

if (!$_POST['copyto_project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if ($_POST['project_id'] == $_POST['copyto_project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$project_name = $project_result->fields["project_name"];

// 取得原始資料
$get_report_sql = "select * from proj".$_POST['project_id']."_report_table where report_id=".$GLOBALS['connection']->QMagic($_POST['report_id']);
$get_report_result = $GLOBALS['connection']->Execute($get_report_sql) or DBError(__FILE__.":".__LINE__);
$line = $get_report_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}
$row = $get_report_result->FetchRow();

$row['created_date'] = date("Y-m-j");
$row['reported_by'] = $_SESSION[SESSION_PREFIX.'uid'];

$log = "<font color=red>Copy From <b>".htmlspecialchars($project_name)."</b> ID:<b>".$_POST['report_id']."</b></font>";

$insert_sql = "insert into proj".$_POST['copyto_project_id']."_report_table(";
$values = "values(";

$first = 1;
$key_array = array_keys($row);
for ($i=0; $i<sizeof($key_array); $i++) {
	if (is_int($key_array[$i])) {
		continue;
	}
	if ($key_array[$i] == "report_id") {
		continue;
	}
	$value = $row[$key_array[$i]];
	if ($value == "") {
		continue;
	}

	if (!$first) {
		$insert_sql .= ",";
		$values .= ",";
	}
	$insert_sql .= $key_array[$i];
	$values .= $GLOBALS['connection']->QMagic($value);
	if ($first) {
		$first = 0;
	}
}
$insert_sql .= ") ".$values.")";

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($insert_sql) or DBError(__FILE__.":".__LINE__);
$copyto_report_id = $GLOBALS['connection']->Insert_ID("proj".$_POST['copyto_project_id']."_report_table", 'report_id');

// Write the "Copy from xxx" in the log table
$now = $GLOBALS['connection']->DBTimeStamp(time());
$insert_sql = "insert into proj".$_POST['copyto_project_id']."_report_log_table( 
			report_id, user_id, post_time, description) values(
			".$GLOBALS['connection']->QMagic($copyto_report_id).", 
			".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).", $now, 
			".$GLOBALS['connection']->QMagic($log).")";
$GLOBALS['connection']->Execute($insert_sql) or DBError(__FILE__.":".__LINE__);

// Copy all logs from the report
$all_log_sql = "select log_id, report_id, user_id, post_time, description,
				filename from proj".$_POST['project_id']."_report_log_table 
				where report_id=".$GLOBALS['connection']->QMagic($_POST['report_id']);
$all_log_result = $GLOBALS['connection']->Execute($all_log_sql) or DBError(__FILE__.":".__LINE__);

$file_array = array();
while ($row = $all_log_result->FetchRow()) {

	$insert_sql = "insert into proj".$_POST['copyto_project_id']."_report_log_table( 
			report_id, user_id, post_time, description, filename, filedata) 
			values(".$GLOBALS['connection']->QMagic($copyto_report_id).", 
			".$GLOBALS['connection']->QMagic($row['user_id']).", 
			".$GLOBALS['connection']->QMagic($row['post_time']).", 
			".$GLOBALS['connection']->QMagic($row['description']).", 
			".$GLOBALS['connection']->QMagic($row['filename']).", (select filedata from 
			proj".$_POST['project_id']."_report_log_table
			where log_id=".$GLOBALS['connection']->QMagic($row['log_id'])."))";
	
	$GLOBALS['connection']->Execute($insert_sql) or DBError(__FILE__.":".__LINE__);
	$copyto_log_id = $GLOBALS['connection']->Insert_ID("proj".$_POST['copyto_project_id']."_report_log_table",
													   'log_id');

	if ($GLOBALS['SYS_FILE_IN_DB'] != 1 && $row['filename']) {
		$file_array[$copyto_log_id] = $row['filename'];
	}
}

// Add see also
$new_seealso_sql = "insert into proj".$_POST['copyto_project_id']."_seealso_table(
					report_id, see_also_project, see_also_id) values(
					".$GLOBALS['connection']->QMagic($copyto_report_id).",
					".$GLOBALS['connection']->QMagic($_POST['project_id']).",
					".$GLOBALS['connection']->QMagic($_POST['report_id']).")";
$GLOBALS['connection']->Execute($new_seealso_sql) or DBError(__FILE__.":".__LINE__);

// Also create see also on source project
$new_seealso_sql = "insert into proj".$_POST['project_id']."_seealso_table(
					report_id, see_also_project, see_also_id) values(
					".$GLOBALS['connection']->QMagic($_POST['report_id']).",
					".$GLOBALS['connection']->QMagic($_POST['copyto_project_id']).",
					".$GLOBALS['connection']->QMagic($copyto_report_id).")";
$GLOBALS['connection']->Execute($new_seealso_sql) or DBError(__FILE__.":".__LINE__);

if ($GLOBALS['SYS_FILE_IN_DB'] != 1) {
	// Copy files to new project
	$key_array = array_keys($file_array);
	for ($i = 0; $i<sizeof($key_array); $i++) {
		$filename = $file_array[$key_array[$i]];
		$dest_file = "upload/project".$_POST['copyto_project_id']."/".$filename;
		$num=1;
		while (file_exists($dest_file)) {
			$num++; // if previous file name existed then thy another number+_+filename
			if ($num > 100) {
				$subname = strrchr($filename, ".");
				if (utf8_strlen($subname) > 250) {
					$filename = date("U");
					$dest_file = "upload/project".$_POST['copyto_project_id']."/".$filename;
				} else {
					$filename = date("U").$subname;
					$dest_file = "upload/project".$_POST['copyto_project_id']."/".$filename;
				}				
			} else {
				$filename = $num."_".$file_array[$key_array[$i]];
				$dest_file = "upload/project".$_POST['copyto_project_id']."/".$filename;
			}
		}

		if (!copy("upload/project".$_POST['project_id']."/".$filename,
				  $dest_file)) {
			die("Fail to copy files.");
		}
		$update_sql = "update proj".$_POST['copyto_project_id']."_report_log_table set
					filename=".$GLOBALS['connection']->QMagic($filename)." where log_id=".$GLOBALS['connection']->QMagic($key_array[$i]);
		$GLOBALS['connection']->Execute($update_sql) or DBError(__FILE__.":".__LINE__);
	}
}

$GLOBALS['connection']->CompleteTrans();

FinishPrintOut("project_list.php?project_id=".$_POST['project_id'], "finish_new", "report");
?>