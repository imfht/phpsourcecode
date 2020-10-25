<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: login.php,v 1.17 2013/07/07 21:29:09 alex Exp $
 *
 */
include("include/db.php");
include("include/misc.php");
include("include/error.php");
include("include/auth_imap.php");
include("include/string_function.php");

function PrintErrorExit($error_key, $arg_key = "", $arg_string = "")
{
    header("Content-type:text/html; charset=utf-8");
	global $STRING;

	$message = $STRING[$error_key];
	if ($message == "") {
		$message = $error_key;
	}
	if ($arg_key != "") {
		$arg_value = $STRING[$arg_key];
		$message = str_replace("@key@", $arg_value, $message);
	}
	if ($arg_string != "") {
		$message = str_replace("@string@", $arg_string, $message);
	}
	echo $message;
	exit(0);
}

function SetLoginLog($user_id)
{
	$now = $GLOBALS['connection']->DBTimeStamp(time());
	$sql = "insert into ".$GLOBALS['BR_login_log_table']."(user_id, login_time)
			values(".$GLOBALS['connection']->QMagic($user_id).", $now)";
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}

if (!isset($_POST['username']) || $_POST['username'] == "") {
	PrintErrorExit("no_empty", "username");
}
if (!isset($_POST['password'])) {
	PrintErrorExit("no_empty", "password");
}

if (($SYSTEM['auth_method'] == "imap") && ($_POST['username'] != "admin")){
	$username = stripslashes($_POST["username"]);
	$password = stripslashes($_POST["password"]);
		
	$Auth = authValidateUser($SYSTEM['imap_server'], $SYSTEM['imap_port'], $username, $password );
	if ($Auth == 1) {
		$sql = "select * from ".$GLOBALS['BR_user_table']." where username=".$GLOBALS['connection']->QMagic($_POST['username'])." and account_disabled!='t'";
		$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

		// 取得 $result 中的列數，0 表示不合法、1 表示合法
		$line = $sql_result->RecordCount();
	} else {
		WriteSyslog("warn", "syslog_login_failed", "", $_POST['username']);
		PrintErrorExit("auth_imap_failed");
	}

} else {
	$sql="select * from ".$GLOBALS['BR_user_table']." where username=".$GLOBALS['connection']->QMagic($_POST['username'])." and password='".md5($_POST['password'])."' and account_disabled!='t'";
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

	// 取得 $result 中的列數，0 表示不合法、1 表示合法
	$line = $sql_result->RecordCount();
}

// 根據合法與否而顯示結果
if ($line == 1) {
	$reg_uid = $sql_result->fields["user_id"];
	$reg_username = $sql_result->fields["username"];
	$reg_email = $sql_result->fields["email"];
	$reg_gid = $sql_result->fields["group_id"];

	session_start();
	$reg_board_read=array();
	$_SESSION[SESSION_PREFIX.'uid'] = $reg_uid;
	$_SESSION[SESSION_PREFIX.'username'] = $reg_username;
	$_SESSION[SESSION_PREFIX.'email'] = $reg_email;
	$_SESSION[SESSION_PREFIX.'gid'] = $reg_gid;
	$_SESSION[SESSION_PREFIX.'board_read'] = $reg_board_read;
	unset($_SESSION[SESSION_PREFIX.'language']);

	$now = $GLOBALS['connection']->DBTimeStamp(time());
	$sql = "update ".$GLOBALS['BR_user_table']." set last_login=$now
			where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

	WriteSyslog("info", "syslog_login", "", $reg_username);
	SetLoginLog($_SESSION[SESSION_PREFIX.'uid']);

	echo 1;
	exit;
} else {
	WriteSyslog("warn", "syslog_login_failed", "", $_POST['username']);
	PrintErrorExit("auth_failed");
}

?>
