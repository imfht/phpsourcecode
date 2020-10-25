<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: logout.php,v 1.5 2013/07/07 21:29:09 alex Exp $
 *
 */
include("include/header.php");

/// 更新使用者讀取看版的日期(更新 project 的 last_read 欄位)
$board_read = $_SESSION[SESSION_PREFIX.'board_read'];
if (sizeof($board_read) != 0){
	$now = $GLOBALS['connection']->DBTimeStamp(time());
	for ($i=0; $i<sizeof($board_read); $i++){
		$lastread_sql="update ".$GLOBALS['BR_proj_access_table']." set last_read=$now
			where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid'])." and 
			project_id=".$GLOBALS['connection']->QMagic($board_read[$i]);
		$GLOBALS['connection']->Execute($lastread_sql) or DBError(__FILE__.":".__LINE__);
	}
}

unset($_SESSION[SESSION_PREFIX.'uid']);
unset($_SESSION[SESSION_PREFIX.'username']);
unset($_SESSION[SESSION_PREFIX.'gid']);
unset($_SESSION[SESSION_PREFIX.'board_read']);

echo "<h2 align=center><a href=index.php>Back to Index</a></h2>"; 
echo "<script>";
echo "location.href = \"index.php\";";
echo "</script>";
include("include/tail.php");
?>