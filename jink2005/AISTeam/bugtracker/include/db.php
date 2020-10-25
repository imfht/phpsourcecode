<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: db.php,v 1.24 2013/07/07 21:31:13 alex Exp $
 *
 */
ini_set('include_path', ".".PATH_SEPARATOR."include".PATH_SEPARATOR."../include".PATH_SEPARATOR.ini_get('include_path'));
include("config.php");
include($GLOBALS["SYS_PROJECT_PATH"]."/adodb5/adodb.inc.php"); 

/* connect to database */
$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;

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
if (!preg_match("/^[_\.0-9A-Za-z-]+@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,4}$/", $SYSTEM['mail_from_email'])) {
	$SYSTEM['mail_from_email'] = "root@".$_SERVER['SERVER_NAME'];
}

?>
