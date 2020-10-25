<?php 
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_delete.php,v 1.9 2013/06/29 20:18:15 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();
	
if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_faq'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['faq_id']) || ($_GET['faq_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "faq_class_id");
}

// Get class data
$faq_class_sql = "select * from ".$GLOBALS['BR_faq_content_table']." where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id']);
$faq_class_result = $GLOBALS['connection']->Execute($faq_class_sql) or DBError(__FILE__.":".__LINE__);
$line = $faq_class_result->Recordcount();
if ($line == 1) {
	$question = $faq_class_result->fields["question"];
	$project_id = $faq_class_result->fields["project_id"];
}else{
	WriteSyslog("error", "syslog_not_found", "faq", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "faq");
}

$GLOBALS['connection']->StartTrans();
		
$delete_sql="delete from ".$GLOBALS['BR_faq_map_table']." where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
    
$delete_sql="delete from ".$GLOBALS['BR_faq_content_table']." where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id']);
$GLOBALS['connection']->Execute($delete_sql) or DBError(__FILE__.":".__LINE__);
	
$GLOBALS['connection']->CompleteTrans();

WriteSyslog("info", "syslog_delete_xxx", "faq", $question);

$extra_params = GetExtraParams($_GET, "search_key,faq_class,page");

FinishPrintOut("faq_admin.php?project_id=$project_id".$extra_params, "finish_delete", "faq");
      
?>