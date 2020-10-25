<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_download.php,v 1.12 2013/06/29 11:33:40 alex Exp $
 *
 */
session_cache_limiter('must-revalidate');
session_start();
include("../include/db.php");
include("../include/group_function.php");

if (!isset($_SESSION[SESSION_PREFIX.'uid']) || !isset($_SESSION[SESSION_PREFIX.'gid']) || 
	!isset($_SESSION[SESSION_PREFIX.'username'])) {
	include("../include/header.php");
	ErrorPrintOut("timeout");
}


if ($_GET['document_id']) {
	$sql = "select filename, filedata from ".$GLOBALS['BR_document_table']."
		where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id'])." and filename!=".$GLOBALS['connection']->QMagic("");
} else if ($_GET['document_history_id']) {
	$sql = "select filename, filedata from ".$GLOBALS['BR_document_history_table']."
		where document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id'])." and filename!=".$GLOBALS['connection']->QMagic("");
} else {
	include("../include/header.php");
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "document_id");
}

InitGroupPrivilege($_SESSION[SESSION_PREFIX.'gid']);

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_document'])) {
	include("../include/header.php");
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	$sql .= " and (allow_other_group='t' or created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).")";
}

$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	include("../include/header.php");
	WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document");
}
$filename = $result->fields["filename"];
$filedata = $result->fields["filedata"];
$filedata = $GLOBALS['connection']->BlobDecode($filedata, 0, 0);

$subname = strrchr($filename, ".");
if (($subname == "") || ($subname == ".")) {
	$type = "application/xxx";
} else {
	$type = $content_type[strtolower($subname)];
	if ($type == "") {
		$type = "application/xxx";
	}
}

header('Content-Type: '.$type);
if (strstr($_SERVER[HTTP_USER_AGENT], "MSIE")) {
	header('Content-Disposition: inline; filename="'.rawurlencode($filename).'"');
} else {
	header('Content-Disposition: inline; filename="'.addslashes($filename).'"');
}

if ($GLOBALS['SYS_FILE_IN_DB']) {
	echo $filedata;
} else {
	if ($_GET['document_history_id']) {
		$path = "documents/history/".$filename;
	} else {
		$path = "documents/".$filename;
	}
	readfile($path);
}
?>
