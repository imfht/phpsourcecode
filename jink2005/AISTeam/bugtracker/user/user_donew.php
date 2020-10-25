<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_donew.php,v 1.11 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/email_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$_POST['username'] = trim($_POST['username']);
if ($_POST['username'] == "") {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST, 
						  "no_empty", "username");
}
if (strcasecmp($_POST['username'], "all") == 0) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST, 
						  "system_reserve_word", "username", $_POST['username']);
}

$pattern = str_replace('\\', '\\\\', $reserve_words);
if (preg_match("/[".$pattern."]/", $_POST['username'])) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST, 
						  "reserve_hint", "username", $reserve_words);
}

if ($_POST['email'] && !IsEmailAddress($_POST['email'])) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST, 
						  "wrong_format", "email");
}

if (($SYSTEM['auth_method'] == "native") && 
	($_POST['password1']  != $_POST['password2'])) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST,
						  "password_not_match");
}

if (utf8_strlen($_POST['username']) > 20) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST,
						  "too_long", "username", "20");
}
if (utf8_strlen($_POST['email']) > 60) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST,
						  "too_long", "email", "60");
}
if (utf8_strlen($_POST['realname']) > 100) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST,
						  "too_long", "realname", "100");
}
if (($SYSTEM['auth_method'] == "native") && (utf8_strlen($_POST['password1']) > 50) ){
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST,
						  "too_long", "password", "50");
}
	
// 先檢查是否有同樣 username 的使用者
$check_user_sql = "select * from ".$GLOBALS['BR_user_table']." where username=".$GLOBALS['connection']->QMagic($_POST['username']);

$check_user_result = $GLOBALS['connection']->Execute($check_user_sql) or DBError(__FILE__.":".__LINE__);
$check_user_line = $check_user_result->Recordcount();
if ($check_user_line>0) {
	ErrorPrintBackFormOut("GET", "user_new.php", $_POST,
						  "have_same", "username", $_POST['username']);
}

if ($SYSTEM['auth_method'] == "native") {
	$md5_passwd = md5($_POST['password1']);
} else {
	$md5_passwd = "imap";
}

$now = $GLOBALS['connection']->DBTimeStamp(time());
$adduser_sql = "insert into ".$GLOBALS['BR_user_table']."(username, group_id, email, realname, created_date, password, language)
				values(".$GLOBALS['connection']->QMagic($_POST['username']).", 
				".$GLOBALS['connection']->QMagic($_POST['group_id']).", 
				".$GLOBALS['connection']->QMagic($_POST['email']).", 
				".$GLOBALS['connection']->QMagic($_POST['realname']).", $now,
				".$GLOBALS['connection']->QMagic($md5_passwd).", 
				".$GLOBALS['connection']->QMagic($_POST['language']).")";

// Get the total number of project
$count_project_sql = "select count(*) from ".$GLOBALS['BR_project_table'];
$count_result = $GLOBALS['connection']->Execute($count_project_sql) or DBError(__FILE__.":".__LINE__);
$count_project = $count_result->fields[0];

// Start the transaction
$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($adduser_sql) or DBError(__FILE__.":".__LINE__);
$user_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_user_table'], 'user_id');

$sql = "delete from ".$GLOBALS['BR_proj_access_table']." where user_id=$user_id";
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
// 如果有設定使用者可以讀該版，則新增使用者
for ($i=0; $i<$count_project; $i++) {
	$project_id="project".$i;
	if (isset($_POST[$project_id])){
		$user_list_sql="insert into ".$GLOBALS['BR_proj_access_table']."(user_id, project_id, last_read) 
			values(".$GLOBALS['connection']->QMagic($user_id).", ".$GLOBALS['connection']->QMagic($_POST[$project_id]).", '1999-01-01')";
		$GLOBALS['connection']->Execute($user_list_sql) or DBError(__FILE__.":".__LINE__);
	}
}
$GLOBALS['connection']->CompleteTrans();

LoadingTimerShow();
SendUpdateUserEamil($_POST['username'], $_POST['password1'], "new");
LoadingTimerHide();

WriteSyslog("info", "syslog_new_xxx", "user", $_POST['username']);
FinishPrintOut("user_admin.php?user_type=".$_POST['user_type'], "finish_new", "user", 0);

?>
