<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_doedit.php,v 1.11 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

// 輸入一個使用者去找出原本使用者的日期
// 如果有找到使用者，則傳回日期
// 否則傳回 "1999-01-01"
function FindLastRead($user ,$orig_user) {
	for($i=0;$i<sizeof($orig_user);$i++) {
		if ($user==$orig_user[$i]->getuserid()) {
			// 為了避免一轉移資料時，使用者並沒有 last_read 的資料
			// 所以如果使用者 last_read 資料是空的，就傳回 1999-01-01
			if ($orig_user[$i]->getmydate()!="") {
				return $orig_user[$i]->getmydate();
			}else{
				return "1999-01-01";
			}
		}else{
			continue;
		}
	}
	// 沒有在陣列中找到，所以是新的使用者，傳回1999-01-01
	return "1999-01-01";
}

// 定義存每一個使用者設定的物件
class userdate {
	var $userid;
	var $mydate;
	function setuserid($id) {
		$this->userid=$id;
	}
	function setmydate($inputdate) {
		$this->mydate=$inputdate;
	}
	function getuserid() {
		return $this->userid;
	}
	function getmydate() {
		return $this->mydate;
	}
}


if (!($GLOBALS['Privilege'] & $GLOBALS['can_update_project'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_POST['project_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (trim($_POST['project_id']) == "") {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
if (trim($_POST['project_name']) == "") {
	ErrorPrintOut("no_empty", "project_name");
}
if (utf8_strlen($_POST['project_name']) > 100) {
	ErrorPrintBackFormOut("GET", "project_edit.php", $_POST, 
						  "too_long", "project_name", "100");
	
}
if (utf8_strlen($_POST['version_pattern']) > 40) {
	ErrorPrintBackFormOut("GET", "project_edit.php", $_POST, 
						  "too_long", "version_pattern", "40");
	
}

$GLOBALS['connection']->StartTrans();

// Remove old mailto users
$delete_old_mailto_sql = "delete from ".$GLOBALS['BR_proj_auto_mailto_table']." where 
	project_id=".$GLOBALS['connection']->QMagic($_POST['project_id'])." and can_unsubscribe!='t'";
$GLOBALS['connection']->Execute($delete_old_mailto_sql) or DBError(__FILE__.":".__LINE__);

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
	// If the user has subscribe by them self, we have to remove it first
	$delete_old_mailto_sql = "delete from ".$GLOBALS['BR_proj_auto_mailto_table']." where 
		project_id=".$GLOBALS['connection']->QMagic($_POST['project_id'])." and 
		user_id=".$GLOBALS['connection']->QMagic($auto_mail_array[$i])." and can_unsubscribe='t'";
	$GLOBALS['connection']->Execute($delete_old_mailto_sql) or DBError(__FILE__.":".__LINE__);

	$auto_email_sql = "insert into ".$GLOBALS['BR_proj_auto_mailto_table']."(project_id, user_id, can_unsubscribe) 
		values(".$GLOBALS['connection']->QMagic($_POST['project_id']).", ".$GLOBALS['connection']->QMagic($auto_mail_array[$i]).", 'f')";
	$GLOBALS['connection']->Execute($auto_email_sql) or DBError(__FILE__.":".__LINE__);
}

// Remove old feedback mailto users
$delete_old_feedback_mailto_sql = "delete from ".$GLOBALS['BR_proj_feedback_mailto_table']." where 
	project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$GLOBALS['connection']->Execute($delete_old_feedback_mailto_sql) or DBError(__FILE__.":".__LINE__);

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
		values(".$GLOBALS['connection']->QMagic($_POST['project_id']).", ".$GLOBALS['connection']->QMagic($feedback_mailto_array[$i]).")";
	$GLOBALS['connection']->Execute($feedback_mailto_sql) or DBError(__FILE__.":".__LINE__);
}

// Remember old last_read for users.
$orig_user_sql = "select * from ".$GLOBALS['BR_proj_access_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$orig_user_result = $GLOBALS['connection']->Execute($orig_user_sql) or DBError(__FILE__.":".__LINE__);

$orig_user = array();
while ($row = $orig_user_result->FetchRow()){
	$user_id = $row["user_id"];
	$last_read = $row["last_read"];
	$new_user = new userdate;
	$new_user->setuserid($user_id);
	$new_user->setmydate($last_read);
	array_unshift($orig_user, $new_user);
}

// 先將原本的列表清除
$delete_access_sql = "delete from ".$GLOBALS['BR_proj_access_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$GLOBALS['connection']->Execute($delete_access_sql) or DBError(__FILE__.":".__LINE__);

$all_allow_uid = explode(",", $_POST['allow_uid']);

for ($i=0; $i<sizeof($all_allow_uid); $i++) {
	if (!is_numeric($all_allow_uid[$i])) {
		continue;
	}
	$now = $GLOBALS['connection']->DBTimeStamp(time());
	$list_sql="insert into ".$GLOBALS['BR_proj_access_table']."(user_id, project_id, last_read) 
			values(".$GLOBALS['connection']->QMagic($all_allow_uid[$i]).", ".$GLOBALS['connection']->QMagic($_POST['project_id']).", $now)";
	$GLOBALS['connection']->Execute($list_sql) or DBError(__FILE__.":".__LINE__);
}

// Check whether the project name is unique.
$check_sql = "select * from ".$GLOBALS['BR_project_table']." 
	where project_id!=".$GLOBALS['connection']->QMagic($_POST['project_id'])." and 
		project_name=".$GLOBALS['connection']->QMagic($_POST['project_name']);
$check_result = $GLOBALS['connection']->Execute($check_sql) or DBError(__FILE__.":".__LINE__);
$line = $check_result->Recordcount();
if ($line != 0) {
	ErrorPrintOut("have_same", "project_name", $_POST['project_name']);
}

// 更新 Pattern of Version 的資料
$update_project="update ".$GLOBALS['BR_project_table']." set 
		project_name=".$GLOBALS['connection']->QMagic($_POST['project_name']).",
		version_pattern=".$GLOBALS['connection']->QMagic($_POST['version_pattern'])."
		where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$GLOBALS['connection']->Execute($update_project) or DBError(__FILE__.":".__LINE__);

// end of transaction
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_edit_xxx", "project", $_POST['project_name']);
FinishPrintOut("../index.php", "finish_update", "project");

?>
