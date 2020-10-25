<?php
ini_set('include_path', ".".PATH_SEPARATOR."include".PATH_SEPARATOR."../include".PATH_SEPARATOR.ini_get('include_path'));
$setup_steps = array(
	"welcome.php",
	"check_env.php",
	"setup_db.php",
	"setup_table.php",
	"setup_string.php",
);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="../style.css?2.4.0" rel="stylesheet" type="text/css">
	<title>Bug Tracker</title>
	<script language="JavaScript" src="../javascript/misc.js?2.4.0" type="text/javascript"></script>
</head>

<body>
<div style="position: absolute; width: 50px; height: 35px; z-index: 1; visibility: visible; left: 12px; top: 6px" id="layer1">
	<img src="../images/top_icon.gif">
</div>
<div style="position: absolute; width: 500px; height: 35px; z-index: 1; visibility: visible; left: 54px; top: 0px" id="layer2">
	<font class="title_header">Bug Tracker</font>
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="40">
	<tr>
		<td width="100%" bgcolor="#3B59A0" nowrap><a name="top">&nbsp;</a></td>
		<td bgcolor="#3B59A0"><img border="0" src="../images/top_header.gif" width="700" height="40"></td>
	</tr>
</table>
<table style="margin-bottom: 45px;" border="0" width="100%" cellpadding="0" cellspacing="0" background="../images/toolbar.png">
	<tr>
		<td width="100%" align="center" height="40">
			&nbsp;
		</td>
	</tr>
</table>

<div id="current_location">
	<b>Current Location:</b> /
	Setup Wizard
	<?php if (isset($_GET['step'])) echo " / Step ".$_GET['step']." of ".(sizeof($setup_steps)-1);?>
</div>
<div id="main_container">

<div id="sub_container" style="width: 98%;">
	<table width="100%" border="0">
		<tr>
			<td align="left" nowrap>
				<tt class="outline">Bug Tracker Setup Wizard</tt>
			</td>
		</tr>
	</table>
	<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
		<h3>&nbsp;</h3>
<?php

$installed = 0;
if (!isset($_GET['step'])) {
	include("../include/config.php");
	include("../adodb5/adodb.inc.php");

	error_reporting(0);

	$GLOBALS['connection'] = &ADONewConnection($GLOBALS['BR_dbtype']);
	$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], $GLOBALS['BR_dbpwd'], $GLOBALS['BR_dbname']);

	$sql = "select count(*) from sysconf_table";

	$result = $GLOBALS['connection']->Execute($sql);
	if ($result->fields[0]) {
		echo "System installed. To re-install, please remove old tables.";
		$installed = 1;
	}
	$_GET['step'] = 0;
}

if (!$installed) {
	$error = 0;
	include($setup_steps[$_GET['step']]);

	if ($error != 0) {
		echo '<form method="GET" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return OnSubmit(this);" name="form1">';
		echo '<input type=hidden name="step" value="'.($_GET['step']).'">';
		echo '<p align="center"><input type="submit" name="next" value="Retest" class="button"></p>';
		echo '</form>';
	} else {
		if ($_GET['step'] == (sizeof($setup_steps)-1)) {
			echo "<p>The default administrator account information is:</p>";
			echo "<ul>";
			echo "<li>username: admin</li>";
			echo "<li>password: admin</li>";
			echo "</ul>";
	
			echo "<p align=\"center\"><a href=\"".$GLOBALS["SYS_URL_ROOT"]."/index.php\">Login Bug Tracker</a></p>";
			echo '<h2 align=center><font color="red">Please remember to remove setup/*.php after installation</font><h1>';
			echo '<h2 align=center><font color="red">Please do not remove the whole setup directory. Remove *.php only.</font><h1>';
		} else {
			echo '<br><form method="GET" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return OnSubmit(this);" name="form1">';
			echo '<input type=hidden name="step" value="'.($_GET['step']+1).'">';
			echo '<p align="center"><input type="submit" name="next" value="Next Step &gt; &gt;" class="button"></p>';
			echo '</form>';
		}
	}
}
?>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<hr>
<table width="90%" align="center" cellpadding="0" border="0" cellspacing="0">
	<tr>
		<td align="center" valign="top">
			<font size="2" color="#C0C0C0">Copyright 2003-2007 Wang, Chun-Pin All rights reserved.</font>
		</td>
	</tr>
</table>

</body>

</html>
