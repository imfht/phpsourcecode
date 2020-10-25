<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: password_dosend.php,v 1.10 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/email_function.php");

function GetNewPassword()
{
	srand((double)microtime()*1000000);
    $str = md5(rand().md5(time()));
	return substr($str, 0, 5).substr($str, 20, 3);
}

if (!$_POST['email']) {
	ErrorPrintOut("no_empty", "email");
}

if (!IsEmailAddress($_POST['email'])) {
	ErrorPrintOut("wrong_format", "email");
}

if ($_POST['register'] == "y") {
	// check user table
	$sql="select * from ".$GLOBALS['BR_customer_user_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email']);
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	if ($sql_result->Recordcount() > 0) {
		ErrorPrintOut("have_same", "user", $_POST['email']);
	}
	// check tmp user table
	$sql="select * from ".$GLOBALS['BR_customer_user_tmp_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email']);
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	if ($sql_result->Recordcount() > 0) {
		ErrorPrintOut("have_same", "user", $_POST['email']);
	}

	$new_password = GetNewPassword();
	$now = $GLOBALS['connection']->DBTimeStamp(time());
	$insert_sql = "insert into ".$GLOBALS['BR_customer_user_tmp_table']."(email, password, created_date)
			values(".$GLOBALS['connection']->QMagic($_POST['email']).", 
			".$GLOBALS['connection']->QMagic(md5($new_password)).", $now)";
	$GLOBALS['connection']->Execute($insert_sql);

	LoadingTimerShow();
	SendRemindPassowrd($_POST['email'], $new_password, 1);
	LoadingTimerHide();

	FinishPrintOut("index.php", "finish_password_send", "", 0);
	

} else {

	$sql="select * from ".$GLOBALS['BR_customer_user_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email'])." and account_disabled!='t'";
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

	// 取得 $sql_result 中的列數，0 表示不合法、1 表示合法
	$line = $sql_result->Recordcount();
echo "<h1>$sql, $line</h1>";
	// 根據合法與否而顯示結果
	if ($line == 1) {

		$delete_sql = "delete from ".$GLOBALS['BR_customer_user_tmp_table']." where email=".$GLOBALS['connection']->QMagic($_POST['email']);
		$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

		$new_password = GetNewPassword();
		$now = $GLOBALS['connection']->DBTimeStamp(time());
		$insert_sql = "insert into ".$GLOBALS['BR_customer_user_tmp_table']."(email, password, created_date)
			values(".$GLOBALS['connection']->QMagic($_POST['email']).", 
			".$GLOBALS['connection']->QMagic(md5($new_password)).", $now)";
		$GLOBALS['connection']->Execute($insert_sql);

		LoadingTimerShow();
		SendRemindPassowrd($_POST['email'], $new_password, 0);
		LoadingTimerHide();

		FinishPrintOut("index.php", "finish_password_send", "", 0);

		// 帳號和密碼輸入錯誤
	} else {
		ErrorPrintOut("no_such_xxx", "user");
	}
}

?>
