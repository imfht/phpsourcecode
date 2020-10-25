<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: header.php,v 1.27 2010/07/27 09:24:17 alex Exp $
 *
 */
session_start();
ini_set('include_path', ".".PATH_SEPARATOR."include".PATH_SEPARATOR."../include".PATH_SEPARATOR.ini_get('include_path'));
include_once("misc.php");
include_once("db.php");
include_once("group_function.php");
include_once("error.php");
include_once("string_function.php");
include_once("auth.php");

if (isset($_SESSION[SESSION_PREFIX.'back_array']) && ($_GET['error_back'] != 1)) {
	unset($_SESSION[SESSION_PREFIX.'back_array']);
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/alexwang/style.css?<?php echo $SYSTEM['version']?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/style.css?<?php echo $SYSTEM['version']?>" rel="stylesheet" type="text/css">
	<title>
<?php
echo $SYSTEM['program_name'];
if (isset($_SESSION[SESSION_PREFIX.'uid']) && isset($_SESSION[SESSION_PREFIX.'username'])) {
   echo "--".$_SESSION[SESSION_PREFIX.'username'];
}
?>
	</title>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/string_js.php?<?php echo $SYSTEM['version'].$_SESSION[SESSION_PREFIX.'language']?>" type="text/javascript"></script>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/misc.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/alexwang/alexwang.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/alexwang/ajax.js" type="text/javascript"></script>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/alexwang/misc.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/alexwang/dialog.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/alexwang/tooltip.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
</head>

<body>
<div style="position: absolute; width: 50px; height: 35px; z-index: 1; visibility: visible; left: 12px; top: 6px" id="layer1">
	<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/top_icon.gif">
</div>
<div style="position: absolute; width: 500px; height: 35px; z-index: 1; visibility: visible; left: 54px; top: 0px" id="layer2">
	<font class="title_header"><?php echo $SYSTEM['program_name']?></font>
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="40">
	<tr>
		<td width="100%" bgcolor="#3B59A0" nowrap><a name="top">&nbsp;</a></td>
		<td bgcolor="#3B59A0"><img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/top_header.gif" width="700" height="40"></td>
	</tr>
</table>

<table style="margin-bottom: 45px;" border="0" width="100%" cellpadding="0" cellspacing="0" background="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/toolbar.png">
	<tr>
	
		<td width="100%" align="center" height="40">
			<span id="search_container"></span>
		</td>
	
<?php
// Function menu for logged in users
if (isset($_SESSION[SESSION_PREFIX.'uid']) && isset($_SESSION[SESSION_PREFIX.'gid']) && isset($_SESSION[SESSION_PREFIX.'username'])) {
	InitGroupPrivilege($_SESSION[SESSION_PREFIX.'gid']);

	echo "<td nowrap>";
	echo "<a href=\"".$GLOBALS["SYS_URL_ROOT"]."/index.php\" class=\"toolbar\">";
	echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/title_project.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_project_list'];
	echo "</a>";
	echo "&nbsp;</td>";
	if ( ($_SESSION[SESSION_PREFIX.'uid'] == 0) ||
		 ($GLOBALS['Privilege'] & $GLOBALS['can_see_document']) ){
		echo "<td nowrap>";
		echo "<a href=\"".$GLOBALS["SYS_URL_ROOT"]."/document/document.php\" class=\"toolbar\">\n";
		echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/title_document.png\" align=\"middle\" border=\"0\">&nbsp;";
		echo $STRING['title_document']."</a>";
		echo "&nbsp;</td>";
	}

	if ( ($_SESSION[SESSION_PREFIX.'uid'] == 0) ||
		 ($GLOBALS['Privilege'] & $GLOBALS['can_see_schedule']) ){
		echo "<td nowrap>";
		echo "<a href=\"".$GLOBALS["SYS_URL_ROOT"]."/schedule/schedule.php?init=y\" class=\"toolbar\">\n";
		echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/title_schedule.png\" align=\"middle\" border=\"0\">&nbsp;";
		echo $STRING['title_schedule']."</a>";
		echo "&nbsp;</td>";
	}

	// Statistic
	echo "<td nowrap>";
	echo "<a href=\"".$GLOBALS["SYS_URL_ROOT"]."/system/system.php?page=information\" class=\"toolbar\">";
	echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/title_information.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_information']."</a>";
	echo "&nbsp;</td>";

	// System
	echo "<td nowrap>";
	echo "<a href=\"".$GLOBALS["SYS_URL_ROOT"]."/system/system.php\" class=\"toolbar\">";
	echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/title_system.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_system']."</a>";
	echo "&nbsp;</td>";

	echo "<td nowrap>";
	echo "<a href=\"".$GLOBALS["SYS_URL_ROOT"]."/logout.php\" class=\"toolbar\">";
	echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/title_logout.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_logout']."</a>";
	echo "&nbsp;</td>";
}
?>
	</tr>
</table>
