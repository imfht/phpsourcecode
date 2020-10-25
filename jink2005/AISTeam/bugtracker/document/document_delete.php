<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_delete.php,v 1.12 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!(isset($_GET['document_id'])) ){
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "document_id");
}
if (!($GLOBALS['Privilege'] & $GLOBALS['can_delete_document'])) {
	// 檢查使用者是否為 admin group 或是該 document 的擁有者
	$owner_sql="select * from ".$GLOBALS['BR_document_table']." 
			where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id'])." and created_by='".$_SESSION[SESSION_PREFIX.'uid']."'";
	$owner_result = $GLOBALS['connection']->Execute($owner_sql) or DBError(__FILE__.":".__LINE__);
	$line = $owner_result->Recordcount();
	if ($line != 1) {
		WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
		ErrorPrintOut("no_such_xxx", "document");
	}
}

if ($GLOBALS['SYS_FILE_IN_DB'] == 0) {
	$file_sql = "SELECT filename FROM ".$GLOBALS['BR_document_history_table']." WHERE document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']);
	$file_result = $GLOBALS['connection']->Execute($file_sql) or DBError(__FILE__.":".__LINE__);
	while ($row = $file_result->FetchRow()) {
		$filename = $row['filename'];
		if ($filename != '') {
			@unlink("documents/history/".$filename);
		}
	}
	$file_sql = "SELECT filename FROM ".$GLOBALS['BR_document_table']." WHERE document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']);
	$file_result = $GLOBALS['connection']->Execute($file_sql) or DBError(__FILE__.":".__LINE__);
	$line = $file_result->Recordcount();
	if ($line == 1) {
		$filename = $file_result->fields["filename"];
		if ($filename != '') {
			@unlink("documents/".$filename);
		}
	}
}

$GLOBALS['connection']->StartTrans();

$delete_sql="delete from ".$GLOBALS['BR_document_map_table']." where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);

$sql = "delete from ".$GLOBALS['BR_document_history_table']." where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$sql = "delete from ".$GLOBALS['BR_document_table']." where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']);
$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$GLOBALS['connection']->CompleteTrans();

$extra_params = GetExtraParams($_GET, "search_key,group_class,page");
if ($extra_params != "") {
	$extra_params = "?".substr($extra_params, 1);
}
FinishPrintOut("document.php".$extra_params, "finish_delete", "document");
?>