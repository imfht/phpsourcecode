<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_doconfig.php,v 1.9 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!IsEmailAddress($_POST['mail_from_email'])) {
	ErrorPrintBackFormOut("GET", "feedback_config.php", $_POST, 
						  "wrong_format", "mail_from_email");
}
if ($_POST['feedback_system_name'] == "") {
	ErrorPrintBackFormOut("GET", "feedback_config.php", $_POST, 
						  "no_empty", "feedback_system_name");
}
if ($_POST['mail_from_name'] == "") {
	ErrorPrintBackFormOut("GET", "feedback_config.php", $_POST, 
						  "no_empty", "mail_from_name");
}
if ($_POST['mail_from_email'] == "") {
	ErrorPrintBackFormOut("GET", "feedback_config.php", $_POST, 
						  "no_empty", "mail_from_name");
}
if ($_POST['import_description'] == "") {
	ErrorPrintBackFormOut("GET", "feedback_config.php", $_POST, 
						  "no_empty", "import_description");
}
if ($_POST['closed_description'] == "") {
	ErrorPrintBackFormOut("GET", "feedback_config.php", $_POST, 
						  "no_empty", "closed_description");
}

$_POST['feedback_system_name'] = htmlspecialchars($_POST['feedback_system_name']);
$_POST['mail_from_name'] = htmlspecialchars($_POST['mail_from_name']);

$import_description = SetAllowHTMLChars($_POST['import_description']);
$import_description = str_replace("  ", "&nbsp;&nbsp;", $import_description);
$import_description = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $import_description);

$closed_description = SetAllowHTMLChars($_POST['closed_description']);
$closed_description = str_replace("  ", "&nbsp;&nbsp;", $closed_description);
$closed_description = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $closed_description);

$config_sql = "update ".$GLOBALS['BR_feedback_config_table']." set 
		feedback_system_name=".$GLOBALS['connection']->QMagic($_POST['feedback_system_name']).", 
		mail_from_name=".$GLOBALS['connection']->QMagic($_POST['mail_from_name']).", 
		mail_from_email=".$GLOBALS['connection']->QMagic($_POST['mail_from_email']).",
		login_mode=".$GLOBALS['connection']->QMagic($_POST['login_mode']).", 
		import_description=".$GLOBALS['connection']->QMagic($import_description).",
		closed_description=".$GLOBALS['connection']->QMagic($closed_description);
   
$GLOBALS['connection']->Execute($config_sql) or DBError(__FILE__.":".__LINE__);

FinishPrintOut("system.php", "finish_update", "feedback_system");
?>

