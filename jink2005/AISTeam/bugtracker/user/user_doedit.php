<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_doedit.php,v 1.16 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/email_function.php");

AuthCheckAndLogin();

// 輸入一個使用者去找出原本使用者的日期
// 如果有找到使用者，則傳回日期
// 否則傳回 "1999-01-01"
function FindLastRead($project, $orig_projects) {
	for($i=0;$i<sizeof($orig_projects);$i++) {
		if ($project==$orig_projects[$i]->getprojectid()) {
			if ($orig_projects[$i]->getlastread()!="") {
				return $orig_projects[$i]->getlastread();
			}else{
				return "1999-01-01";
			}
		}else{
			continue;
		}
	}
	// 沒有在陣列中找到，所以是新的程式，傳回1999-01-01
	return "1999-01-01";
}

if (!isset($_POST['user_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "user_id");
}

if ($_SESSION[SESSION_PREFIX.'uid'] != $_POST['user_id']) {
	if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_privilege");
	}
} else {
	if (!($GLOBALS['Privilege'] & $GLOBALS['can_edit_selfdata'])) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_privilege");
	}
}

$change_password = 0;
if (isset($_POST['password1'])) {
	if (($_POST['password1'] != "12345678") || ($_POST['password2'] != "AlexWang")) {
		$change_password = 1;
	}
}

if (($change_password) && 
	($_POST['password1'] != $_POST['password2']) ) {
	ErrorPrintOut("password_not_match");
}

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	if ($_POST['email'] && !IsEmailAddress($_POST['email'])) {
		ErrorPrintOut("wrong_format", "email");
	}
} else {
	if (!IsEmailAddress($_POST['email'])) {
		ErrorPrintOut("wrong_format", "email");
	}
}

if (isset($_POST['enable_login'])) {
	if ($_POST['enable_login'] == 1) {
		$account_disabled = "account_disabled='f',";
	} else {
		$account_disabled = "account_disabled='t',";
	}
} else {
	$account_disabled="";
}

$send_email = "";

// 如果有設密碼才要修改密碼
if ($change_password == 0) {
	$update_user_sql = "update ".$GLOBALS['BR_user_table']." set email=".$GLOBALS['connection']->QMagic($_POST['email']).",
		realname=".$GLOBALS['connection']->QMagic($_POST['realname']).", 
		language=".$GLOBALS['connection']->QMagic($_POST['language']).", $account_disabled
		group_id=".$GLOBALS['connection']->QMagic($_POST['group_id'])." where user_id=".$GLOBALS['connection']->QMagic($_POST['user_id']);
} else {
	$update_user_sql = "update ".$GLOBALS['BR_user_table']." set email=".$GLOBALS['connection']->QMagic($_POST['email']).", 
		password=".$GLOBALS['connection']->QMagic(md5($_POST['password1'])).",
		realname=".$GLOBALS['connection']->QMagic($_POST['realname']).", 
		language=".$GLOBALS['connection']->QMagic($_POST['language']).", $account_disabled
		group_id=".$GLOBALS['connection']->QMagic($_POST['group_id'])." where user_id=".$GLOBALS['connection']->QMagic($_POST['user_id']);
	
	if (($_POST['enable_login'] == 1) || ($_POST['user_id'] == 0)) {
		$send_email = "password";
	}		
}

// Get original username in order to send email.
$get_user_sql = "select username, account_disabled from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_POST['user_id']);
$user_result = $GLOBALS['connection']->Execute($get_user_sql);
if ($user_result->Recordcount() != 1) {
	ErrorPrintOut("no_such_xxx", "user");
}
$username = $user_result->fields["username"];

// 開始"交易"
$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($update_user_sql) or DBError(__FILE__.":".__LINE__);

if (($_POST['user_id'] != 0) && 
	( ($_SESSION[SESSION_PREFIX.'uid'] == 0) || 
	  ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user']) ) ) {
	// 記住之前可以讀的有哪些程式，及其最後時間
	$all_access_sql = "select * from ".$GLOBALS['BR_proj_access_table']." where user_id=".$GLOBALS['connection']->QMagic($_POST['user_id']);
	$all_access_result = $GLOBALS['connection']->Execute($all_access_sql) or DBError(__FILE__.":".__LINE__);

	$orig_projects=array();
	while ($row = $all_access_result->FetchRow()) {
		$project_id = $row["project_id"];
		$last_read = $row["last_read"];
		$new_project = new projectclass;
		$new_project->setprojectid($project_id);
		$new_project->setlastread($last_read);;
		array_unshift($orig_projects, $new_project);
	}

	// 取得現有的程式討論區數量
	$project_sql = "select * from ".$GLOBALS['BR_project_table'];
	$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
	$count_project = $project_result->Recordcount();
	
	$delete_access_sql = "delete from ".$GLOBALS['BR_proj_access_table']." where user_id=".$GLOBALS['connection']->QMagic($_POST['user_id']);
	$GLOBALS['connection']->Execute($delete_access_sql) or DBError(__FILE__.":".__LINE__);
	
	// 如果有設定使用者可以讀該版，則新增使用者
	for ($i=0; $i<$count_project; $i++) {
		$project_arg = "project".$i;
		if (isset($_POST[$project_arg])){
			
			$access_sql="insert into ".$GLOBALS['BR_proj_access_table']."(user_id, project_id, last_read) 
				values(".$GLOBALS['connection']->QMagic($_POST['user_id']).", 
				".$GLOBALS['connection']->QMagic($_POST[$project_arg]).", 
				".$GLOBALS['connection']->QMagic(FindLastRead($_POST[$project_arg], $orig_projects)).")";

			$GLOBALS['connection']->Execute($access_sql) or DBError(__FILE__.":".__LINE__);
		}
	}
} 

$GLOBALS['connection']->CompleteTrans();

if ($_SESSION[SESSION_PREFIX.'uid'] == $_POST['user_id']) {
	unset($_SESSION[SESSION_PREFIX.'language']);
}

if ($send_email) {
	LoadingTimerShow();
	SendUpdateUserEamil($username, $_POST['password1'], $send_email);
	LoadingTimerHide();
}

if (($_SESSION[SESSION_PREFIX.'uid'] == 0) || ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("info", "syslog_edit_xxx", "user", $username);
	FinishPrintOut("user_admin.php?user_type=".$_POST['user_type'], "finish_update", "user", 0);
} else {
	FinishPrintOut("../system/system.php", "finish_update", "user", 0);
}
?>

