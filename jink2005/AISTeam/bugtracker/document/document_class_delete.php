<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_class_delete.php,v 1.5 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();
	
if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_document_class'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['document_class_id']) || ($_GET['document_class_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "id");
}

// Get class data
$document_class_sql = "select * from ".$GLOBALS['BR_document_class_table']." where document_class_id=".$GLOBALS['connection']->QMagic($_GET['document_class_id']);
$document_class_result = $GLOBALS['connection']->Execute($document_class_sql) or DBError(__FILE__.":".__LINE__);
$line = $document_class_result->Recordcount();
if ($line == 1) {
	$class_name = $document_class_result->fields["class_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "document_class", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document_class");
}
		
$GLOBALS['connection']->StartTrans();
		
$delete_sql="delete from ".$GLOBALS['BR_document_map_table']." where document_class_id=".$GLOBALS['connection']->QMagic($_GET['document_class_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$delete_sql="delete from ".$GLOBALS['BR_document_class_table']." where document_class_id=".$GLOBALS['connection']->QMagic($_GET['document_class_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
	
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "document_class", $class_name);
FinishPrintOut("document_class.php", "finish_delete", "document_class");

?>