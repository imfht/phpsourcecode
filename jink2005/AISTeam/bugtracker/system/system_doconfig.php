<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: system_doconfig.php,v 1.12 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!IsEmailAddress($_POST['mail_from_email'])) {
	ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
						  "wrong_format", "mail_from_email");
}
if ($_POST['program_name'] == "") {
	ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
						  "no_empty", "program_name");
}
if ($_POST['mail_from_name'] == "") {
	ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
						  "no_empty", "mail_from_name");
}
if ($_POST['mail_from_email'] == "") {
	ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
						  "no_empty", "mail_from_name");
}
if ($_POST['auth_method'] == "imap") {
	if ($_POST['imap_server'] == "") {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "no_empty", "imap_server");
	}
	if ($_POST['imap_port'] == "") {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "no_empty", "imap_port");
	}
	if (!is_numeric($_POST['imap_port'])) {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "wrong_format", "imap_port");
	}
}

if ($_POST['imap_port'] == "") {
	$_POST['imap_port'] = 143;
}

if ($_POST['auto_redirect'] != 't') {
	$_POST['auto_redirect'] = 'f';
}

if ($_POST['mail_function'] != "nosend" && $_POST['mail_function'] != "smtp") {
	$_POST['mail_function'] = "mail";
}

if ($_POST['mail_function'] == "smtp") {
	if ($_POST['mail_smtp_server'] == "") {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "no_empty", "mail_smtp_server");
	}
	if ($_POST['mail_smtp_port'] == "") {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "no_empty", "mail_smtp_port");
	}
	if (!is_numeric($_POST['mail_smtp_port'])) {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "wrong_format", "mail_smtp_port");
	}
}
if ($_POST['mail_smtp_port'] == "") {
	$_POST['mail_smtp_port'] = 25;
}
if ($_POST['mail_smtp_auth'] != 't') {
	$_POST['mail_smtp_auth'] = 'f';
}
if ($_POST['mail_smtp_auth'] == "t") {
	if ($_POST['mail_smtp_user'] == "") {
		ErrorPrintBackFormOut("GET", "system_config.php", $_POST, 
							  "no_empty", "mail_smtp_user");
	}
	if (($_POST['mail_smtp_password'] == "12345678") && ($_POST['mail_verify_password'] == "AlexWang") ){
		$change_password = 0;
	} else {
		$change_password = 1;
	}
	if (($change_password) && 
		($_POST['mail_smtp_password'] != $_POST['mail_verify_password']) ) {
		ErrorPrintOut("password_not_match");
	}
	if ($change_password) {
		$change_password_sql = "mail_smtp_password=".$GLOBALS['connection']->QMagic($_POST['mail_smtp_password']).",";
	}
}

$_POST['program_name'] = htmlspecialchars($_POST['program_name']);
$_POST['mail_from_name'] = htmlspecialchars($_POST['mail_from_name']);

$config_sql = "update ".$GLOBALS['BR_sysconf_table']." set 
		program_name=".$GLOBALS['connection']->QMagic($_POST['program_name']).", 
		date_format = ".$GLOBALS['connection']->QMagic($_POST['date_format']).",
		auto_redirect=".$GLOBALS['connection']->QMagic($_POST['auto_redirect']).", 
		auth_method=".$GLOBALS['connection']->QMagic($_POST['auth_method']).",
		imap_server=".$GLOBALS['connection']->QMagic($_POST['imap_server']).", 
		imap_port=".$GLOBALS['connection']->QMagic($_POST['imap_port']).",

		mail_from_name=".$GLOBALS['connection']->QMagic($_POST['mail_from_name']).", 
		mail_from_email=".$GLOBALS['connection']->QMagic($_POST['mail_from_email']).",
		mail_function=".$GLOBALS['connection']->QMagic($_POST['mail_function']).", 
		mail_smtp_server=".$GLOBALS['connection']->QMagic($_POST['mail_smtp_server']).", 
		mail_smtp_port=".$GLOBALS['connection']->QMagic($_POST['mail_smtp_port']).", 
		mail_smtp_auth=".$GLOBALS['connection']->QMagic($_POST['mail_smtp_auth']).",
		mail_smtp_user=".$GLOBALS['connection']->QMagic($_POST['mail_smtp_user']).", ".$change_password_sql."
	
		allow_subscribe='".$_POST['allow_subscribe']."', 
		max_area='".$_POST['max_area']."', max_minor_area='".$_POST['max_minor_area']."',
		max_filter_per_user='".$_POST['max_filter_per_user']."', max_shared_filter='".$_POST['max_shared_filter']."',
		max_syslog='".$_POST['max_syslog']."'";

$GLOBALS['connection']->Execute($config_sql) or DBError(__FILE__.":".__LINE__);

FinishPrintOut("system.php", "finish_update", "system_config");
?>

