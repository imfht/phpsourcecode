<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: group_doedit.php,v 1.8 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/status_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if ((!isset($_POST['group_id'])) || ($_POST['group_id'] == 0)) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "group_id");
}
$_POST['group_name'] = trim($_POST['group_name']);
if (!$_POST['group_name']) {
	ErrorPrintOut("no_empty", "group_name");
}
if (strcasecmp($_POST['group_name'], "all") == 0) {
	ErrorPrintOut("system_reserve_word", "group_name", $_POST['group_name']);
}
if (utf8_strlen($_POST['group_name']) > 50) {
	ErrorPrintOut("too_long", "group_name", "50");
}

$check_group_sql = "select * from ".$GLOBALS['BR_group_table']." where 
	group_name=".$GLOBALS['connection']->QMagic($_POST['group_name'])." and 
	group_id!=".$GLOBALS['connection']->QMagic($_POST['group_id']);
$check_group_result = $GLOBALS['connection']->Execute($check_group_sql) or DBError(__FILE__.":".__LINE__);
$check_group_line = $check_group_result->Recordcount();
if ($check_group_line > 0) {
	ErrorPrintBackFormOut("GET", "group_edit.php?group_id=".$_POST['group_id'], $_POST, 
						  "have_same", "group_name", htmlspecialchars($_POST['group_name']));
}

$privilege = 0;
for ($i=0; $i<sizeof($privilege_array); $i++) {
	if (isset($_POST[$privilege_array[$i]])) {
		$privilege |= $GLOBALS[$privilege_array[$i]];
	}
}

$GLOBALS['connection']->StartTrans();

$update_sql = "update ".$GLOBALS['BR_group_table']." set 
		group_name=".$GLOBALS['connection']->QMagic($_POST['group_name']).", privilege=$privilege
		where group_id=".$GLOBALS['connection']->QMagic($_POST['group_id']);

$GLOBALS['connection']->Execute($update_sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->Execute("delete from ".$GLOBALS['BR_group_allow_status_table']." 
		where group_id=".$GLOBALS['connection']->QMagic($_POST['group_id'])) or DBError(__FILE__.":".__LINE__);

$status_array = GetStatusArray();
for ($i=0; $i<sizeof($status_array); $i++) {
	$status_arg = "C".$i;
	if (isset($_POST[$status_arg])) {
		$status_sql = "insert into ".$GLOBALS['BR_group_allow_status_table']."(group_id, status_id)
			values(".$GLOBALS['connection']->QMagic($_POST['group_id']).", ".$GLOBALS['connection']->QMagic($_POST[$status_arg]).")";
		$GLOBALS['connection']->Execute($status_sql) or DBError(__FILE__.":".__LINE__);
	}
}
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_edit_xxx", "group", $_POST['group_name']);
FinishPrintOut("group_admin.php", "finish_update", "group");

?>
