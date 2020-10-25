<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_history_restore.php,v 1.5 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

function MoveOldFileToDir($old_dir, $new_dir, $old_filename, $remove)
{
	$new_old_filename = $old_filename;
	$dest_file = $new_dir."/".$old_filename;
	$num=1;
	while (file_exists($dest_file)) {
		if ($num > 100) {
			$subname = strrchr($old_filename, ".");
			if (utf8_strlen($subname) > 250) {
				$new_old_filename = date("U");
				$dest_file = $new_dir."/".$new_old_filename;
			} else {
				$new_old_filename = date("U").$subname;
				$dest_file = $new_dir."/".$new_old_filename;
			}				
		} else {
			$new_old_filename = $num."_".$old_filename;
			$dest_file = $new_dir."/".$new_old_filename;
		}
		$num++; // if previous file name existed then thy another number+_+filename
	}
	if ($remove) {
		rename($old_dir."/".$old_filename, $dest_file);
	} else {
		copy($old_dir."/".$old_filename, $dest_file);
	}
	
	return $new_old_filename;
}

if (!$_GET['document_history_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "document_id");
}

$sql = "SELECT document_id,filename FROM ".$GLOBALS['BR_document_history_table']."
		WHERE document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id']);

$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document");
}
$document_id = $result->fields["document_id"];
$old_history_name = $result->fields["filename"];

$sql = "select created_by,filename from ".$GLOBALS['BR_document_table']." 
		where document_id=".$document_id;
	
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document");
}
$old_filename = $result->fields["filename"];

if (!($GLOBALS['Privilege'] & $GLOBALS['can_update_document']) && 
	($created_by != $_SESSION[SESSION_PREFIX.'uid'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($GLOBALS['SYS_FILE_IN_DB'] == 0) {

	if ($old_filename) {
		$new_old_filename = MoveOldFileToDir("documents", "documents/history", $old_filename, 1);
	}
	if ($old_history_name) {
		$new_history_filename = MoveOldFileToDir("documents/history", "documents", $old_history_name, 0);
	}
}

$history_sql = "INSERT INTO ".$GLOBALS['BR_document_history_table']."(
				document_id, subject, created_by, created_date, description, filename, filedata)
				SELECT document_id,
				subject, created_by, last_update as created_date, description, filename, filedata
                FROM ".$GLOBALS['BR_document_table']." WHERE document_id=".$document_id;

$now = $GLOBALS['connection']->DBTimeStamp(time());
$restore_sql = "UPDATE ".$GLOBALS['BR_document_table']." SET
				subject=(SELECT subject FROM ".$GLOBALS['BR_document_history_table']." WHERE document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id'])."),
				created_by=(SELECT created_by FROM ".$GLOBALS['BR_document_history_table']." WHERE document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id'])."),
				last_update=$now,
				description=(SELECT description FROM ".$GLOBALS['BR_document_history_table']." WHERE document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id'])."),
				filename=(SELECT filename FROM ".$GLOBALS['BR_document_history_table']." WHERE document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id'])."),
				filedata=(SELECT filedata FROM ".$GLOBALS['BR_document_history_table']." WHERE document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id']).")
                WHERE document_id=".$document_id;

$GLOBALS['connection']->StartTrans();

$GLOBALS['connection']->Execute($history_sql) or DBError(__FILE__.":".__LINE__);
$new_document_history_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_document_history_table'], 'document_history_id');

$GLOBALS['connection']->Execute($restore_sql) or DBError(__FILE__.":".__LINE__);

if ($GLOBALS['SYS_FILE_IN_DB'] == 0) {
	if (isset($new_old_filename)) {
		$sql = "UPDATE ".$GLOBALS['BR_document_history_table']." SET filename=".$GLOBALS['connection']->QMagic($new_old_filename)." WHERE document_history_id=".$GLOBALS['connection']->QMagic($new_document_history_id);
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}
	if (isset($new_history_filename)) {
		$sql = "UPDATE ".$GLOBALS['BR_document_table']." SET filename=".$GLOBALS['connection']->QMagic($new_history_filename)." WHERE document_id=".$document_id;
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}
}

$GLOBALS['connection']->CompleteTrans();

FinishPrintOut("document_show.php?document_id=".$document_id, "finish_update", "document");

include("../include/tail.php");
?>