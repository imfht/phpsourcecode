<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_doedit.php,v 1.8 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!isset($_POST['customer_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_id");
}

$sql = "select customer_id from ".$GLOBALS['BR_customer_table']." where customer_id=".$GLOBALS['connection']->QMagic($_POST['customer_id']);
$result = $GLOBALS['connection']->Execute($sql);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer");
}

if (!trim($_POST['customer_name'])) {
	ErrorPrintBackFormOut("GET", "customer_edit.php", $_POST, 
						  "no_empty", "customer_name");
}

if (utf8_strlen($_POST['customer_name']) > 100) {
	ErrorPrintBackFormOut("GET", "customer_edit.php", $_POST,
						  "too_long", "customer_name", "100");
}
if (utf8_strlen($_POST['address']) > 150) {
	ErrorPrintBackFormOut("GET", "customer_edit.php", $_POST,
						  "too_long", "address", "150");
}
if (utf8_strlen($_POST['tel']) > 20) {
	ErrorPrintBackFormOut("GET", "customer_edit.php", $_POST,
						  "too_long", "tel", "20");
}
if (utf8_strlen($_POST['fax']) > 20) {
	ErrorPrintBackFormOut("GET", "customer_edit.php", $_POST,
						  "too_long", "fax", "20");
}
	
if ($_POST['customer_id'] == 0) {
	$sql = "update ".$GLOBALS['BR_customer_table']." set
		address=".$GLOBALS['connection']->QMagic($_POST['address']).", 
		tel=".$GLOBALS['connection']->QMagic($_POST['tel']).", 
		fax=".$GLOBALS['connection']->QMagic($_POST['fax'])." where customer_id='".$_POST['customer_id']."'";
} else {
	$sql = "update ".$GLOBALS['BR_customer_table']." set customer_name=".$GLOBALS['connection']->QMagic($_POST['customer_name']).",
		address=".$GLOBALS['connection']->QMagic($_POST['address']).", 
		tel=".$GLOBALS['connection']->QMagic($_POST['tel']).", 
		fax=".$GLOBALS['connection']->QMagic($_POST['fax'])." where customer_id='".$_POST['customer_id']."'";
}


// 取得現有的程式討論區數量
$count_project_sql = "select count(*) from ".$GLOBALS['BR_project_table'];
$count_result = $GLOBALS['connection']->Execute($count_project_sql) or DBError(__FILE__.":".__LINE__);
$count_project = $count_result->fields[0];

// 開始"交易"
$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$sql = "delete from ".$GLOBALS['BR_proj_customer_access_table']." where customer_id=".$GLOBALS['connection']->QMagic($_POST['customer_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);


for ($i=0; $i<$count_project; $i++) {
	$project_id="project".$i;
	if (isset($_POST[$project_id])){
		$sql = "insert into ".$GLOBALS['BR_proj_customer_access_table']."(customer_id, project_id) 
				values(".$GLOBALS['connection']->QMagic($_POST['customer_id']).", ".$GLOBALS['connection']->QMagic($_POST[$project_id]).")";
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}
}
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_edit_xxx", "customer", $_POST['customer_name']);
FinishPrintOut("customer_admin.php", "finish_update", "customer");

?>
