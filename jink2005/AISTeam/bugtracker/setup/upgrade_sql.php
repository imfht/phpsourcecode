<?php
if (!$_SESSION['reg_upgrade_allowed']) {
	echo "Authentication failed";
}
function UpgradeSQL()
{
	global $SYSTEM;
	global $SYSTEM_VERSION;

	if ($SYSTEM['version'] == "") {
		echo "Failed to get system version.(".__LINE__.")";
		return -1;
	}
	if ($SYSTEM_VERSION == "") {
		echo "Failed to get system version.(".__LINE__.")";
		return -1;
	}
	$GLOBALS['connection']->StartTrans();

	$OldVersion = explode(".", $SYSTEM['version']);
	$NewVersion = explode(".", $SYSTEM_VERSION);

	for ($i = $OldVersion[0]; $i <= $NewVersion[0]; $i++) {
		for ($j = $OldVersion[1]; $j < $NewVersion[1]; $j++) {
			$SqlFile = "sql/upgrade.".$i.".".$j."-".$i.".".($j+1).".".$GLOBALS['BR_dbtype'];

			if (file_exists($SqlFile)) {
				echo "<p>running $SqlFile</p>";
				$sql = fread(fopen($SqlFile, "r"), filesize($SqlFile));
				$sql_array = explode(";", $sql);
				for ($k = 0; $k < sizeof($sql_array) - 1; $k++) {
					$sql_array[$k] = str_replace("\n", "", $sql_array[$k]);
					$result = $GLOBALS['connection']->Execute($sql_array[$k]);
					if (!$result) {
						$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b></p>';
						break;
					}
				}
			} else {
				//echo "File $SqlFile does not exist";
			}

			$SqlFile = "sql/upgrade.".$i.".".$j."-".$i.".".($j+1).".sql";
			if (file_exists($SqlFile)) {
				echo "<p>running $SqlFile</p>";
				$sql = fread(fopen($SqlFile, "r"), filesize($SqlFile));
				$sql_array = explode(";", $sql);
				for ($k = 0; $k < sizeof($sql_array) - 1; $k++) {
					$sql_array[$k] = str_replace("\n", "", $sql_array[$k]);
					$result = $GLOBALS['connection']->Execute($sql_array[$k]);
					if (!$result) {
						$reason = '<p><b>Error: '.$GLOBALS['connection']->ErrorMsg().'</b></p>';
						break;
					}
				}
			} else {
				//echo "File $SqlFile does not exist";
			}

			$PHPFile = "sql/upgrade.".$i.".".$j."-".$i.".".($j+1).".php";
			if (file_exists($PHPFile)) {
				echo "<p>running $PHPFile</p>";
				include($PHPFile);
			} else {
				//echo "File $SqlFile does not exist";
			}
		}
	}
	echo "<ul>";
	echo "<li>Update database scheme: ";
	if ($reason) {
		$GLOBALS['connection']->RollbackTrans();
		echo '<font color="red"><b>FAILED</b></font></li>';
		echo '</ul>';
		echo '<p>Please see the error message and try to correct it.</p>';
		echo $reason;
		return -1;
	} else {
		$GLOBALS['connection']->CompleteTrans();
		echo '<font color="green"><b>PASSED!</b></font></li>';
		echo '</ul>';
		return 0;
	}
}
$error = UpgradeSQL();
?>
