<?php
/* Copyright c 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: label.php,v 1.5 2013/06/30 21:45:28 alex Exp $
 *
 */
session_start();
include("../include/db.php");
include("../include/misc.php");
include("../include/string_function.php");
include("../include/error.php");
include("../include/auth.php");
include("../include/project_function.php");
include("../include/group_function.php");

function ErrorOut($Message)
{
	header("Content-type:text/html; charset=utf-8");
	echo $Message;
	exit(0);
}

function PrintOut($Message)
{
	header("Content-type:text/html; charset=utf-8");
	echo $Message;
	exit(0);
}

if (!isset($_SESSION[SESSION_PREFIX.'uid']) || !isset($_SESSION[SESSION_PREFIX.'gid']) ||
	!isset($_SESSION[SESSION_PREFIX.'username'])) {
	ErrorOut($STRING['timeout']);
}

InitGroupPrivilege($_SESSION[SESSION_PREFIX.'gid']);

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_label'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorOut($STRING["no_privilege"]);
}

if (!$_POST['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	$message = str_replace("@key@", $STRING['project'], $STRING['no_such_xxx']);
	ErrorOut($message);
}
if (CheckProjectAccessable($_POST['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	$message = str_replace("@key@", $STRING['project'], $STRING['no_such_xxx']);
	ErrorOut($message);
}

switch ($_POST['action']) {
case 'new':
	$_POST['label_name'] = trim($_POST['label_name']);
	if ($_POST['label_name'] == "") {
		$message = str_replace("@key@", $STRING['label'], $STRING['no_empty']);
		ErrorOut($message);
	}
	if (utf8_strlen($_POST['label_name']) > 30) {
		$message = str_replace("@key@", $STRING['label'], $STRING['too_long']);
		$message = str_replace("@string@", $_POST['label_name'], $message);
		ErrorOut($message);
	}

	$_POST['label_name'] = htmlspecialchars($_POST['label_name']);
	$color = rand(0, sizeof($label_color_array)-1);

	
	// Check wheter we have the same label in the system
	$sql = "SELECT label_id FROM ".$GLOBALS['BR_label_table']." WHERE label_name=".$GLOBALS['connection']->QMagic($_POST['label_name'])." and 
			project_id=".$GLOBALS['connection']->QMagic($_POST['project_id']);
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line != 0) {
		$message = str_replace("@key@", $STRING['label'], $STRING['have_same']);
		$message = str_replace("@string@", stripslashes(htmlspecialchars_decode($_POST['label_name'])), $message);
		ErrorOut($message);
	}

	// Create the label
	$GLOBALS['connection']->StartTrans();

	$sql = "INSERT INTO ".$GLOBALS['BR_label_table']."(project_id, label_name, color)
			VALUES(".$GLOBALS['connection']->QMagic($_POST['project_id']).", 
			".$GLOBALS['connection']->QMagic($_POST['label_name']).", ".$color.")";
	
	$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$label_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_label_table'], 'label_id');  

	$bugids = explode(",", $_POST['ids']);
	for ($i = 0; $i < sizeof($bugids); $i++) {
		$sql = "INSERT INTO ".$GLOBALS['BR_label_mapping_table']."(report_id, label_id)
		    VALUES(".$GLOBALS['connection']->QMagic($bugids[$i]).", ".$GLOBALS['connection']->QMagic($label_id).")";
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}

	$GLOBALS['connection']->CompleteTrans();

	WriteSyslog("info", "syslog_new_xxx", "label", $_POST['label_name']);

	$message = "{label_id:$label_id, font_color: '".$label_color_array[$color][0]."', background_color: '".$label_color_array[$color][1]."'}";
	PrintOut($message);
	break;
case 'apply':

	$sql = "SELECT count(*) from ".$GLOBALS['BR_label_table']." WHERE
		label_id=".$GLOBALS['connection']->QMagic($_POST['label_id']);
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line == 0) {
		$message = str_replace("@key@", $STRING['label'], $STRING['no_such_xxx']);
		ErrorOut($message);
	}

	$GLOBALS['connection']->StartTrans();
	$bugids = explode(",", $_POST['ids']);
	for ($i = 0; $i < sizeof($bugids); $i++) {
		$sql = "DELETE FROM ".$GLOBALS['BR_label_mapping_table']."
		    WHERE report_id=".$GLOBALS['connection']->QMagic($bugids[$i])." and label_id=".$GLOBALS['connection']->QMagic($_POST['label_id']);
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
		$sql = "INSERT INTO ".$GLOBALS['BR_label_mapping_table']."(report_id, label_id)
		    VALUES(".$GLOBALS['connection']->QMagic($bugids[$i]).", ".$GLOBALS['connection']->QMagic($_POST['label_id']).")";
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}

	$GLOBALS['connection']->CompleteTrans();

	PrintOut(0);
	break;
	
	break;
case 'remove':
	$GLOBALS['connection']->StartTrans();

	$bugids = explode(",", $_POST['ids']);
	for ($i = 0; $i < sizeof($bugids); $i++) {
		$sql = "DELETE FROM ".$GLOBALS['BR_label_mapping_table']."
		    WHERE label_id=".$GLOBALS['connection']->QMagic($_POST['label_id'])." and report_id=".$GLOBALS['connection']->QMagic($bugids[$i]);
		$GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	}

	$GLOBALS['connection']->CompleteTrans();

	PrintOut(0);
	break;
}
