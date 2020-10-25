<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_class_doedit.php,v 1.6 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_document_class'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_POST['document_class_id']) || ($_POST['document_class_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "id");
}

$_POST['class_name'] = trim($_POST['class_name']);
if (!$_POST['class_name']) {
	ErrorPrintBackFormOut("GET", "document_class.php", $_POST, 
						  "no_empty", "class_name");
}

if (utf8_strlen($_POST['class_name']) > 64) {
	ErrorPrintBackFormOut("GET", "document_class.php", $_POST,
						  "too_long", "class_name", "64");
}

$pattern = str_replace('\\', '\\\\', $reserve_words);
if (preg_match("/[".$pattern."]/", $_POST['class_name'])) {
	ErrorPrintBackFormOut("GET", "document_class.php", $_POST, 
						  "reserve_hint", "class_name", $reserve_words);
}

// Check category name
$check_sql = "select * from ".$GLOBALS['BR_document_class_table']." 
			where class_name='".$_POST['class_name']."' and document_class_id!=".$GLOBALS['connection']->QMagic($_POST['document_class_id']);

$check_result = $GLOBALS['connection']->Execute($check_sql) or  
			DBError(__FILE__.":".__LINE__);

$line = $check_result->Recordcount();
if ($line > 0) {
	ErrorPrintBackFormOut("GET", "document_class.php", $_POST,
						  "have_same", "class_name", $_POST['class_name']);
}

$sql = "update ".$GLOBALS['BR_document_class_table']." set 
		class_name=".$GLOBALS['connection']->QMagic($_POST['class_name'])." where document_class_id=".$GLOBALS['connection']->QMagic($_POST['document_class_id']);

$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

WriteSyslog("info", "syslog_edit_xxx", "document_class", $_POST['class_name']);
FinishPrintOut("document_class.php", "finish_update", "document_class");

?>
