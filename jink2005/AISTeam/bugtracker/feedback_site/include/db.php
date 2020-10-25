<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: db.php,v 1.21 2013/07/07 21:25:44 alex Exp $
 *
 */
ini_set('include_path', ".".PATH_SEPARATOR."include".PATH_SEPARATOR."../include".PATH_SEPARATOR.ini_get('include_path'));
include("adodb5/adodb.inc.php");
include("config.php");

/* connect to database */
$GLOBALS['connection'] = ADONewConnection($GLOBALS['BR_dbtype']);
$GLOBALS['connection']->debug = $GLOBALS['BR_dbdebug'];
$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], $GLOBALS['BR_dbpwd'], $GLOBALS['BR_dbname']) or die($GLOBALS['connection']->ErrorMsg());

if (strstr($GLOBALS['BR_dbtype'], "mysql")) {
	$GLOBALS['connection']->Execute("SET CHARACTER SET utf8");
} else if (strstr($GLOBALS['BR_dbtype'], "postgres")) {
	// The PostgreSQL 9 change the bytea output to hex by default. We need to change it to escape.
	@$GLOBALS['connection']->Execute("set bytea_output = escape");
}

$system_sql = "select * from ".$GLOBALS['BR_sysconf_table'];
$system_result = $GLOBALS['connection']->Execute($system_sql) or die(__FILE__.":".__LINE__.":".$GLOBALS['connection']->ErrorMsg());
if (!$system_result) {
    print $GLOBALS['connection']->ErrorMsg();
	die("Failed to get system configuration.");
}
$SYSTEM = $system_result->FetchRow();

$feedback_system_sql = "select * from ".$GLOBALS['BR_feedback_config_table'];
$feedback_system_result = $GLOBALS['connection']->Execute($feedback_system_sql) or die(__FILE__.":".__LINE__.":".$GLOBALS['connection']->ErrorMsg());
if (!$feedback_system_result) {
	print $GLOBALS['connection']->ErrorMsg();
	die("Failed to get system configuration.");
}

$FEEDBACK_SYSTEM = $feedback_system_result->FetchRow();

if ($FEEDBACK_SYSTEM['login_mode'] == "mode_disabled") {
	echo "The system has be disable now. Please try again later.";
	exit;
}
if (!preg_match("/^[_\.0-9A-Za-z-]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,4}$/", $FEEDBACK_SYSTEM['mail_from_email'])) {
	$FEEDBACK_SYSTEM['mail_from_email'] = "root@".$FEEDBACK_SYSTEM['SERVER_NAME'];
}
?>
