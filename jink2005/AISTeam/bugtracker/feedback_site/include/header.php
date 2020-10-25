<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: header.php,v 1.21 2009/07/07 15:13:52 alex Exp $
 *
 */
session_start();
ini_set('include_path', ".".PATH_SEPARATOR."include".PATH_SEPARATOR."../include".PATH_SEPARATOR.ini_get('include_path'));
include("db.php");
include("misc.php");
include("string_function.php");
include("error.php");
include("auth.php");

if (isset($_SESSION[SESSION_PREFIX.'feedback_back_array']) && ($_GET['error_back'] != 1)) {
	unset($_SESSION[SESSION_PREFIX.'feedback_back_array']);
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="javascript/alexwang/style.css?<?php echo $SYSTEM['version']?>" rel="stylesheet" type="text/css">
	<link href="style.css?<?php echo $SYSTEM['version']?>" rel="stylesheet" type="text/css">
	<title>
<?php
echo $FEEDBACK_SYSTEM['feedback_system_name'];
if (isset($_SESSION[SESSION_PREFIX.'feedback_uid']) && isset($_SESSION[SESSION_PREFIX.'feedback_email'])) {
   echo "--".$_SESSION[SESSION_PREFIX.'feedback_email'];
}
?>
	</title>
	<script language="JavaScript" src="javascript/string_js.php?<?php echo $SYSTEM['version'].$_SESSION[SESSION_PREFIX.'language']?>" type="text/javascript"></script>
	<script language="JavaScript" src="javascript/misc.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="javascript/alexwang/alexwang.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="javascript/alexwang/ajax.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="javascript/alexwang/misc.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="javascript/alexwang/dialog.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
	<script language="JavaScript" src="javascript/alexwang/tooltip.js?<?php echo $SYSTEM['version']?>" type="text/javascript"></script>
</head>

<body>

<div style="position: absolute; width: 500px; height: 35px; z-index: 1; visibility: visible; left: 12px; top: 3px" id="layer1">
	<font class="title_header"><b><?php echo $FEEDBACK_SYSTEM['feedback_system_name']?></b></font>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="40">
	<tr>
		<td width="100%" bgcolor="white" nowrap><a name="top">&nbsp;</a></td>
		<td bgcolor="white"><img border="0" src="images/top_header.gif" width="700" height="40"></td>
	</tr>
</table>
<?php
if (strstr($_SERVER['PHP_SELF'], "project_list.php") || 
	strstr($_SERVER['PHP_SELF'], "faq.php")) {
?>
<script language="JavaScript">
<!--
function onLocalSubmit1(form) {
	if (document.form1 && document.form1.project_id) {
		var project_idx = document.form1.project_id.selectedIndex;
		document.search_form.project_id.value = document.form1.project_id.options[project_idx].value;
	}
	return OnSubmit(form);
}
//-->
</script>
<?php
}
?>
<table style="margin-bottom: 45px;" border="0" width="100%" cellpadding="0" cellspacing="0" background="images/toolbar.png">
	<tr>
		<td width="100%" align="center" height="40">
			<span id="search_container">&nbsp;</span>
		</td>
<?php
// Function menu for logged in users
if (isset($_SESSION[SESSION_PREFIX.'feedback_uid']) && isset($_SESSION[SESSION_PREFIX.'feedback_email'])) {

	echo "<td nowrap>";
	echo "<a href=\"index.php\" class=\"toolbar\">";
	echo "<img src=\"images/title_project.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_project_list'];
	echo "</a>";
	echo "&nbsp;</td>";

	echo "<td nowrap>";
	echo "<a href=\"faq.php\" class=\"toolbar\">";
	echo "<img src=\"images/title_faq.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['faq']."</a>";
	echo "&nbsp;</td>";

	echo "<td nowrap>";
	echo "<a href=\"system.php\" class=\"toolbar\">";
	echo "<img src=\"images/title_system.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_system']."</a>";
	echo "&nbsp;</td>";

	echo "<td nowrap>";
	echo "<a href=\"logout.php\" class=\"toolbar\">";
	echo "<img src=\"images/title_logout.png\" align=\"middle\" border=\"0\">&nbsp;";
	echo $STRING['title_logout']."</a>";
	echo "&nbsp;</td>";
}
?>
  </tr>
</table>
