<?php
/* Copyright c 2003-2006 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_dosubscribe.php,v 1.6 2013/07/05 22:40:48 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

if ($SYSTEM['allow_subscribe'] != 't') {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (($_SESSION[SESSION_PREFIX.'uid'] != 0) || !isset($_GET['user_id'])) {
	echo ".....";
	$_GET['user_id'] = $_SESSION[SESSION_PREFIX.'uid'];
}

$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
       ErrorPrintOut("no_such_xxx", "project");
}

$sql = "select * from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and 
	user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if (($_GET['subscribe'] == 'y') && ($line == 0)) {

	$insert_sql = "insert into ".$GLOBALS['BR_proj_auto_mailto_table']."(project_id, user_id, can_unsubscribe) 
	values(".$GLOBALS['connection']->QMagic($_GET['project_id']).", ".$GLOBALS['connection']->QMagic($_GET['user_id']).", 't')";
	$GLOBALS['connection']->Execute($insert_sql) or DBError(__FILE__.":".__LINE__);

} else if (($_GET['subscribe'] == 'n') && ($line > 0) && ($result->fields["can_unsubscribe"] == 't')) {
	$delete_sql = "delete from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])."
		and user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
	$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
}

if ($_GET['subscribe'] == 'y') {
	FinishPrintOut("../index.php", "subscribe_hint");
} else {
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		FinishPrintOut("project_subscribe.php?project_id=".$_GET['project_id'], "finish_delete", "subscribe");
	} else {
		FinishPrintOut("../index.php", "finish_delete", "subscribe");
	}
}
?>
