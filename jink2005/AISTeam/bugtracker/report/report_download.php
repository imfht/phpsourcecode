<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_download.php,v 1.13 2013/06/30 21:45:28 alex Exp $
 *
 */
session_cache_limiter('must-revalidate');
session_start();
include("../include/db.php");
include("../include/misc.php");
include("../include/project_function.php");

if ((!$_GET['project_id']) || (!$_GET['log_id'])){
	include("../include/header.php");
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "file");
}

if (!isset($_SESSION[SESSION_PREFIX.'uid']) || !isset($_SESSION[SESSION_PREFIX.'gid']) ||
	!isset($_SESSION[SESSION_PREFIX.'username'])) {
	include("../include/header.php");
	ErrorPrintOut("timeout");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	include("../include/header.php");
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$sql = "select filename, filedata from proj".$_GET['project_id']."_report_log_table
		where log_id=".$GLOBALS['connection']->QMagic($_GET['log_id'])." and filename!=".$GLOBALS['connection']->QMagic("");
$result = $GLOBALS['connection']->Execute($sql) or die("Failed to get report data");
if ($result->Recordcount() != 1) {
	include("../include/header.php");
	WriteSyslog("error", "syslog_not_found", "file", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "file");
}
$filename = $result->fields["filename"];
$filedata = $result->fields["filedata"];
$filedata =  $GLOBALS['connection']->BlobDecode($filedata, 0, 0);

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

echo $filedata;
?>
