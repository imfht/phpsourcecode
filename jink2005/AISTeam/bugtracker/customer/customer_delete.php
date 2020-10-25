<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_delete.php,v 1.9 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['customer_id']) || ($_GET['customer_id'] == 0)) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_id");
}
$sql = "select customer_name from ".$GLOBALS['BR_customer_table']." 
	where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer");
}
$customer_name = $result->fields["customer_name"];


$GLOBALS['connection']->StartTrans();
	
$delete_sql="delete from ".$GLOBALS['BR_customer_user_table']." where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$delete_sql="delete from ".$GLOBALS['BR_customer_table']." where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$project_array = GetAllProjects();
for ($i = 0; $i<sizeof($project_array); $i++) {
	$sql = "update proj".$project_array[$i]->getprojectid()."_report_table set
		reported_by_customer=null where reported_by_customer=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

	$sql = "delete from ".$GLOBALS['BR_proj_customer_access_table']."
		   where project_id=".$project_array[$i]->getprojectid()." and 
		   customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

	$sql = "update proj".$project_array[$i]->getprojectid()."_feedback_table set
		customer_id=null where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
}
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "customer", $customer_name);
FinishPrintOut("customer_admin.php", "finish_delete", "customer");
?>
