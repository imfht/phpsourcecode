<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_user_delete.php,v 1.10 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['customer_user_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_user_id");
}

$sql = "select customer_id,email from ".$GLOBALS['BR_customer_user_table']." 
	where customer_user_id=".$GLOBALS['connection']->QMagic($_GET['customer_user_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer_user", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer_user");
}
$email = $result->fields["email"];
$customer_id = $result->fields["customer_id"];

$GLOBALS['connection']->StartTrans();
	
$delete_sql="delete from ".$GLOBALS['BR_customer_user_table']." where customer_user_id=".$GLOBALS['connection']->QMagic($_GET['customer_user_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$delete_sql="delete from ".$GLOBALS['BR_customer_user_tmp_table']." where email=".$GLOBALS['connection']->QMagic($email);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "customer_user", $email);
FinishPrintOut("customer_user_admin.php?customer_id=$customer_id", "finish_delete", "customer_user");

?>
