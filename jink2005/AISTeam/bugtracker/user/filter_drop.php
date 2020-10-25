<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: filter_drop.php,v 1.8 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!(isset($_GET['filter_id'])) ){
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "filter_id");
}
// 檢查使用者是否為 root 或是該程式的擁有者
$owner_sql="select * from ".$GLOBALS['BR_filter_table']." 
		where filter_id=".$GLOBALS['connection']->QMagic($_GET['filter_id'])." and 
		user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$owner_result = $GLOBALS['connection']->Execute($owner_sql) or DBError(__FILE__.":".__LINE__);
$line = $owner_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "filter", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "filter");
}

$GLOBALS['connection']->StartTrans();

// 檢查 usersetting 中，此 filter 是否為 default_filter，如果是就清除
$check_default_sql = "select default_filter from ".$GLOBALS['BR_user_table']." where 
	user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$check_default_result = $GLOBALS['connection']->Execute($check_default_sql) or DBError(__FILE__.":".__LINE__);
$default_filter = $check_default_result->fields["default_filter"];
if ($default_filter == $_GET['filter_id']) {
	$clear_default="update ".$GLOBALS['BR_user_table']." set default_filter=0 where 
		user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
	$GLOBALS['connection']->Execute($clear_default) or DBError(__FILE__.":".__LINE__);
}
$filter_sql = "delete from ".$GLOBALS['BR_filter_table']." where filter_id=".$GLOBALS['connection']->QMagic($_GET['filter_id']);
$GLOBALS['connection']->Execute($filter_sql) or DBError(__FILE__.":".__LINE__);
	
$GLOBALS['connection']->CompleteTrans();
	
if ($_GET['project_id'] != "") {
	FinishPrintOut("filter_setting.php?project_id=".$_GET['project_id'], "finish_delete", "filter");
} else {
	FinishPrintOut("filter_setting.php", "finish_delete", "filter");
}

?>
