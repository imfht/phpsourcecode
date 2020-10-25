<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: login.php,v 1.13 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/db.php");
include("include/misc.php");
include("include/error.php");
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

if (!isset($_POST['email']) || $_POST['email'] == "") {
	PrintErrorExit("no_empty", "email");
}

if (!$_POST['password']) {
	PrintErrorExit("no_empty", "password");
}

$the_day = $GLOBALS['connection']->DBTimeStamp(time() - 864000);
$sql = "delete from ".$GLOBALS['BR_customer_user_tmp_table']." where created_date < ".$the_day."";
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->StartTrans();

$auth = false;
$sql="select * from ".$GLOBALS['BR_customer_user_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email'])." and 
	password=".$GLOBALS['connection']->QMagic(md5($_POST['password']))."  and account_disabled!='t'";
$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$line = $sql_result->Recordcount();

if ($line == 1) {
	$customer_user_id = $sql_result->fields["customer_user_id"];
	$customer_id = $sql_result->fields["customer_id"];
	$email = $sql_result->fields["email"];
	$auth = true;

// ±b¸¹©M±K½X¿é¤J¿ù»~
} else {
	// Check the customer_user_tmp_table
	$sql="select * from ".$GLOBALS['BR_customer_user_tmp_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email'])." and password=".$GLOBALS['connection']->QMagic(md5($_POST['password']));
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

	if ($sql_result->Recordcount() == 1) {
		$sql="select * from ".$GLOBALS['BR_customer_user_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email']);
		$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
		if ($sql_result->Recordcount() == 1) {
			// Exist user
			$customer_user_id = $sql_result->fields["customer_user_id"];
			$customer_id = $sql_result->fields["customer_id"];
			$email = $sql_result->fields["email"];
			$account_disabled = $sql_result->fields["account_disabled"];
			if ($account_disabled == 't') {
				$auth = false;
			} else {
				// Forget password, update new password
				$sql = "update ".$GLOBALS['BR_customer_user_table']." set password=".$GLOBALS['connection']->QMagic(md5($_POST['password']))."
						where customer_user_id=".$GLOBALS['connection']->QMagic($customer_user_id);
				$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
				$auth = true;
			}
		} else {
			// Anonymous can not login under customer mode.
			if ($FEEDBACK_SYSTEM['login_mode'] == "mode_customer") {
					$auth = false;
			} else {
				$now = $GLOBALS['connection']->DBTimeStamp(time());

				// Regester new user
				$sql = "insert into ".$GLOBALS['BR_customer_user_table']."(
					customer_id, email, password, language, created_date) values(0,
					".$GLOBALS['connection']->QMagic($_POST['email']).", 
					".$GLOBALS['connection']->QMagic(md5($_POST['password'])).", '', $now)";
				$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
				$customer_user_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_customer_user_table'], 'customer_user_id');

				$customer_id = 0;
				$email = $_POST['email'];
				$auth = true;
			}
		}
	} else {
		// User doesn't exist in tmp table
		$auth = false;
	}
}

if ($customer_id == 0) {
	// Anonymous can not login under customer mode.
	if ($FEEDBACK_SYSTEM['login_mode'] == "mode_customer") {
		$auth = false;
	}
} else {
	if ($FEEDBACK_SYSTEM['login_mode'] == "mode_anonymous") {
		$auth = false;
	}
}

if ($auth === true) {
	session_start();

	$_SESSION[SESSION_PREFIX.'feedback_uid'] = $customer_user_id;
	$_SESSION[SESSION_PREFIX.'feedback_customer'] = $customer_id;
	$_SESSION[SESSION_PREFIX.'feedback_email'] = $email;
	unset($_SESSION[SESSION_PREFIX.'language']);

	$delete_sql = "delete from ".$GLOBALS['BR_customer_user_tmp_table']."
				where email=".$GLOBALS['connection']->QMagic($_POST['email']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

	$now = $GLOBALS['connection']->DBTimeStamp(time());
	$sql = "update ".$GLOBALS['BR_customer_user_table']." set last_login=$now 
			where customer_user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_uid']);
	$GLOBALS['connection']->Execute($sql);

	$GLOBALS['connection']->CompleteTrans();

	WriteSyslog("info", "syslog_login", "", $email);

	echo 1;

} else {
	$GLOBALS['connection']->CompleteTrans();
	WriteSyslog("warn", "syslog_login_failed", "", $_POST['email']);
	PrintErrorExit("auth_failed");
}
?>
