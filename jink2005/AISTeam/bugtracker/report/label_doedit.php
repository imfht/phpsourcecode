<?php
/* Copyright c 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: label_doedit.php,v 1.3 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_label'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!$_POST['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (!$_POST['label_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "id");
}

if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$_POST['label_name'] = trim($_POST['label_name']);
if ($_POST['label_name'] == "") {
	ErrorPrintOut("no_empty", "label");
}

if (utf8_strlen($_POST['label_name']) > 30) {
	ErrorPrintOut("too_long", "label", 30);
}

$_POST['label_name'] = htmlspecialchars($_POST['label_name']);

if ($_POST['color'] >= sizeof($label_color_array)) {
	$_POST['color'] = 0;
}

// Check wheter we have the same label in the system
$sql = "SELECT label_id FROM ".$GLOBALS['BR_label_table'].
	" WHERE project_id='".$_POST['project_id']."' and label_name=".$GLOBALS['connection']->QMagic($_POST['label_name'])." and 
	label_id!=".$GLOBALS['connection']->QMagic($_POST['label_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 0) {
	ErrorPrintOut("have_same", "label", $_POST['label_name']);
}

$sql = "UPDATE ".$GLOBALS['BR_label_table']." SET label_name=".$GLOBALS['connection']->QMagic($_POST['label_name']).", 
	color=".$GLOBALS['connection']->QMagic($_POST['color'])." WHERE label_id=".$GLOBALS['connection']->QMagic($_POST['label_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

WriteSyslog("info", "syslog_edit_xxx", "label", $_POST['label_name']);
FinishPrintOut("label_admin.php?project_id=".$_POST['project_id'], "finish_update", "label");

?>
