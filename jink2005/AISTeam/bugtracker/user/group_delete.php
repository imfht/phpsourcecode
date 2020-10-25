<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: group_delete.php,v 1.9 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if ((!isset($_GET['group_id'])) || ($_GET['group_id'] == 0)) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "group_id");
}

$sql = "select group_name from ".$GLOBALS['BR_group_table']." where group_id=".$GLOBALS['connection']->QMagic($_GET['group_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "group", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "group");
}
$group_name = $result->fields["group_name"];

$delete_sql="delete from ".$GLOBALS['BR_group_table']." where group_id=".$GLOBALS['connection']->QMagic($_GET['group_id']);
$update_user="update ".$GLOBALS['BR_user_table']." set group_id=-1 where group_id=".$GLOBALS['connection']->QMagic($_GET['group_id']);
$delete_status_sql = "delete from ".$GLOBALS['BR_group_allow_status_table']." where group_id=".$GLOBALS['connection']->QMagic($_GET['group_id']);
$update_document_sql = "update ".$GLOBALS['BR_document_table']." set group_class=0";

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($update_user) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($delete_status_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->Execute($update_document_sql) or DBError(__FILE__.":".__LINE__);
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "group", $group_name);
FinishPrintOut("group_admin.php", "finish_delete", "group");
?>
