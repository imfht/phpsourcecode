<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: area_dolist.php,v 1.9 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_update_project'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
// 先檢查是否有下列資料
if (!isset($_POST['project_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_arg", "project_id");
}
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];

if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$GLOBALS['connection']->StartTrans();
$GLOBALS['connection']->Execute("delete from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_POST['project_id'])) or 
	DBError(__FILE__.":".__LINE__);

for ($i = 0; $i < $SYSTEM['max_area']; $i++) {
	$area_arg = "area".$i;
	$area_owner_arg = "area_owner".$i;
	$_POST[$area_arg] = trim($_POST[$area_arg]);

	if (utf8_strlen($_POST[$area_arg]) > 40) {
		ErrorPrintBackFormOut("GET", "area_list.php?project_id=".$_POST['project_id'], $_POST,
						  "too_long", "area", "40");
	}
	if ($_POST[$area_arg] != "") {
		$sql = "insert into ".$GLOBALS['BR_proj_area_table']."(project_id,
				area_name, area_parent, owner) values(".$GLOBALS['connection']->QMagic($_POST['project_id']).", 
				".$GLOBALS['connection']->QMagic($_POST[$area_arg]).", 0,
				".$GLOBALS['connection']->QMagic($_POST[$area_owner_arg]).")";

		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
		$area_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_proj_area_table'], 'area_id');

		for ($j=0; $j < $SYSTEM['max_minor_area']; $j++) {
			$minor_area_arg = "minor_area".$i."_".$j;
			$minor_area_owner_arg = "minor_area_owner".$i."_".$j;
			$_POST[$minor_area_arg] = trim($_POST[$minor_area_arg]);
			if ($_POST[$minor_area_arg] != "") {
				$sql = "insert into ".$GLOBALS['BR_proj_area_table']."(project_id,
					area_name, area_parent, owner) values(".$GLOBALS['connection']->QMagic($_POST['project_id']).", 
					".$GLOBALS['connection']->QMagic($_POST[$minor_area_arg]).", 
					$area_id, ".$GLOBALS['connection']->QMagic($_POST[$minor_area_owner_arg]).")";
				$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
			}
		}
	}
}
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_edit_xxx", "area_minor_area", $project_name);
FinishPrintOut("project_edit.php?project_id=".$_POST['project_id'], "finish_new", "area_minor_area");

include("../include/tail.php");
?>
