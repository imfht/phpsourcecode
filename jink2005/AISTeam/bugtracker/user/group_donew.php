<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: group_donew.php,v 1.9 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/status_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
$_POST['group_name'] = trim($_POST['group_name']);
if ($_POST['group_name'] == "") {
	ErrorPrintBackFormOut("GET", "group_new.php", $_POST, 
						  "no_empty", "group_name");
}
if (strcasecmp($_POST['group_name'], "all") == 0) {
	ErrorPrintBackFormOut("GET", "group_new.php", $_POST, 
						  "system_reserve_word", "group_name", $_POST['group_name']);
}
if (utf8_strlen($_POST['group_name']) > 50) {
	ErrorPrintBackFormOut("GET", "group_new.php", $_POST, 
						  "too_long", "group_name", "50");
}


// 先檢查是否有同樣 usergroup
$check_group_sql = "select * from ".$GLOBALS['BR_group_table']." where group_name=".$GLOBALS['connection']->QMagic($_POST['group_name']);
$check_group_result = $GLOBALS['connection']->Execute($check_group_sql) or 
	DBError(__FILE__.":".__LINE__);
$check_group_line = $check_group_result->Recordcount();
if ($check_group_line > 0) {
	ErrorPrintBackFormOut("GET", "group_new.php", $_POST, 
						  "have_same", "group_name", htmlspecialchars($_POST['group_name']));
}

$privilege = 0;
for ($i=0; $i<sizeof($privilege_array); $i++) {
	if (isset($_POST[$privilege_array[$i]])) {
		$privilege |= $GLOBALS[$privilege_array[$i]];
	}
}

$new_group_sql="insert into ".$GLOBALS['BR_group_table']."(group_name, privilege) 
	values(".$GLOBALS['connection']->QMagic($_POST['group_name']).", $privilege)";

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute($new_group_sql) or DBError(__FILE__.":".__LINE__);
$group_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_group_table'], 'group_id');

$status_array = GetStatusArray();
for ($i=0; $i<sizeof($status_array); $i++) {
	$status_arg = "C".$i;
	if (isset($_POST[$status_arg])) {
		$status_sql = "insert into ".$GLOBALS['BR_group_allow_status_table']."(group_id, status_id)
			values($group_id, ".$GLOBALS['connection']->QMagic($_POST[$status_arg]).")";
		$GLOBALS['connection']->Execute($status_sql) or DBError(__FILE__.":".__LINE__);
	}
}
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_new_xxx", "group", $_POST['group_name']);
FinishPrintOut("group_admin.php", "finish_new", "group");

?>
