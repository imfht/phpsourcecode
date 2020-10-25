<?php
/* Copyright c 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: label_delete.php,v 1.4 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_label'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!$_GET['label_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "id");
}

$sql = "SELECT * FROM ".$GLOBALS['BR_label_table']." WHERE label_id=".$GLOBALS['connection']->QMagic($_GET['label_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);;
$line = $result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "label");
} 
$project_id = $result->fields["project_id"];
$label_name = $result->fields["label_name"];

if (CheckProjectAccessable($project_id, $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$GLOBALS['connection']->StartTrans();

$sql = "DELETE FROM ".$GLOBALS['BR_label_table']." WHERE label_id=".$GLOBALS['connection']->QMagic($_GET['label_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$sql = "DELETE FROM ".$GLOBALS['BR_label_mapping_table']." WHERE label_id=".$GLOBALS['connection']->QMagic($_GET['label_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "label", $label_name);
FinishPrintOut("label_admin.php?project_id=".$project_id, "finish_delete", "label");

?>
