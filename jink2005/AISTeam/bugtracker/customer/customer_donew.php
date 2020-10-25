<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_donew.php,v 1.8 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!trim($_POST['customer_name'])) {
	ErrorPrintBackFormOut("GET", "customer_new.php", $_POST, 
						  "no_empty", "customer_name");
}

if (utf8_strlen($_POST['customer_name']) > 100) {
	ErrorPrintBackFormOut("GET", "customer_new.php", $_POST,
						  "too_long", "customer_name", "100");
}
if (utf8_strlen($_POST['address']) > 150) {
	ErrorPrintBackFormOut("GET", "customer_new.php", $_POST,
						  "too_long", "address", "150");
}
if (utf8_strlen($_POST['tel']) > 20) {
	ErrorPrintBackFormOut("GET", "customer_new.php", $_POST,
						  "too_long", "tel", "20");
}
if (utf8_strlen($_POST['fax']) > 20) {
	ErrorPrintBackFormOut("GET", "customer_new.php", $_POST,
						  "too_long", "fax", "20");
}
	
// Check whether we have the same customer
$check_customer_sql="select * from ".$GLOBALS['BR_customer_table']." 
				where customer_name=".$GLOBALS['connection']->QMagic($_POST['customer_name']);

$check_customer_result = $GLOBALS['connection']->Execute($check_customer_sql) or 
		DBError(__FILE__.":".__LINE__);

$line = $check_customer_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "customer_new.php", $_POST,
						  "have_same", "customer_name", $_POST['customer_name']);
}

$now = $GLOBALS['connection']->DBTimeStamp(time());
$sql = "insert into ".$GLOBALS['BR_customer_table']."(customer_name, 
		created_date, address, tel, fax)
		values(".$GLOBALS['connection']->QMagic($_POST['customer_name']).", $now, 
		".$GLOBALS['connection']->QMagic($_POST['address']).", 
		".$GLOBALS['connection']->QMagic($_POST['tel']).", 
		".$GLOBALS['connection']->QMagic($_POST['fax']).")";

$count_project_sql = "select count(*) from ".$GLOBALS['BR_project_table'];
$count_result = $GLOBALS['connection']->Execute($count_project_sql) or DBError(__FILE__.":".__LINE__);
$count_project = $count_result->fields[0];
 

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$customer_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_customer_table'], 'customer_id');

$sql = "delete from ".$GLOBALS['BR_proj_customer_access_table']." where customer_id=$customer_id";
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);


for ($i=0; $i<$count_project; $i++) {
	$project_id = "project".$i;
	if (isset($_POST[$project_id])){
		$sql = "insert into ".$GLOBALS['BR_proj_customer_access_table']."(customer_id, project_id) 
				values(".$GLOBALS['connection']->QMagic($customer_id).", ".$GLOBALS['connection']->QMagic($_POST[$project_id]).")";
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}
}
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_new_xxx", "customer", $_POST['customer_name']);
FinishPrintOut("customer_admin.php", "finish_new", "customer");

?>
