<?php
function UpgradeVersion()
{
	global $SYSTEM_VERSION;

	include ("../include/db.php");

	$sql = "update ".$GLOBALS['BR_sysconf_table']." set version=".$GLOBALS['connection']->QMagic($SYSTEM_VERSION);
	$result = $GLOBALS['connection']->Execute($sql);

	echo "<ul>";
	echo "<li>Update version: ";
	if (!$result) {
		echo '<font color="red"><b>FAILED</b></font></li>';
		echo '</ul>';
		echo '<p>Please see the error message and try to correct it.</p>';
		return -1;
	} else {
		echo '<font color="green"><b>PASSED!</b></font></li>';
		echo '</ul>';
		return 0;
	}
}
$error = UpgradeVersion();
