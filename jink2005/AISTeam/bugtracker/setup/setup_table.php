<?php
function CheckTables()
{
	include("../include/config.php");
	include($GLOBALS["SYS_PROJECT_PATH"]."/adodb5/adodb.inc.php");

	$error_reporting = ini_get('error_reporting');
	error_reporting($error_reporting &  ~E_NOTICE);

	$GLOBALS['connection'] = &ADONewConnection($GLOBALS['BR_dbtype']);
	$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], $GLOBALS['BR_dbpwd'], $GLOBALS['BR_dbname']);

	if (strstr($GLOBALS['BR_dbtype'], "mysql")) {
		$GLOBALS['connection']->Execute("SET CHARACTER SET utf8");
	}

	//$GLOBALS['connection']->debug = true;
	$GLOBALS['connection']->StartTrans();
	
	$error_message = "";
	$message = "";

	// Create system tables
	$SqlFile = "sql/setup.".$GLOBALS['BR_dbtype'];
	$fd = fopen($SqlFile, "r");
	if (!$fd) {
		$result = FALSE;
		$error_message .= "<li>Failed to open $SqlFile</li>";
	} else {
		$sql = fread($fd, filesize($SqlFile));
		$sql_array = explode(";", $sql);
		for ($i = 0; $i < sizeof($sql_array) - 1; $i++) {
			$sql_array[$i] = str_replace("\n", "", $sql_array[$i]);
			$result = $GLOBALS['connection']->Execute($sql_array[$i]);
			if ($result == FALSE) {
				$error_message .= "<li>".$GLOBALS['connection']->ErrorMsg()."</li>";
				break;
			}
		}
		fclose($fd);
	}

	// Create system default data
	if ($error_message == "") {
		$SqlFile = "sql/setup.sql";
		$fd = fopen($SqlFile, "r");
		if (!$fd) {
			$result = FALSE;
			$error_message .= "<li>Failed to open $SqlFile</li>";
		} else {
			$sql = fread($fd, filesize($SqlFile));
			$sql_array = explode(";", $sql);
			for ($i = 0; $i < sizeof($sql_array) - 1; $i++) {
				$sql_array[$i] = str_replace("\n", "", $sql_array[$i]);
				$result = $GLOBALS['connection']->Execute($sql_array[$i]);
				if (!$result) {
					$error_message .= "<li>".$GLOBALS['connection']->ErrorMsg()."</li>";
					break;
				}
			}
			fclose($fd);
		}
	}

	echo "<ul>";
	echo "<li>Create system tables: ";
	if (!$result) {
		echo '<font color="red"><b>FAILED</b></font></li>';
		$message .= "<li>Can't create tables. If tables exist, please remove them and setup agdain.</li>";
	} else {
		echo '<font color="green"><b>PASSED!</b></font></li>';

		// Update system default according to your system
		$sql = "update ".$GLOBALS['BR_sysconf_table']." set 
			mail_from_email=".$GLOBALS['connection']->QMagic("root@".$_SERVER['SERVER_NAME']);
		$result1 = $GLOBALS['connection']->Execute($sql);
		if (!$result1) {
			$error_message .= "<li>".$GLOBALS['connection']->ErrorMsg()."</li>";
		}

		$sql = "update ".$GLOBALS['BR_feedback_config_table']." set 
			mail_from_email=".$GLOBALS['connection']->QMagic("root@".$_SERVER['SERVER_NAME']);
		$result2 = $GLOBALS['connection']->Execute($sql);
		if (!$result2) {
			$error_message .= "<li>".$GLOBALS['connection']->ErrorMsg()."</li>";
		}

		$sql = "update ".$GLOBALS['BR_sysconf_table']." set 
			version=".$GLOBALS['connection']->QMagic($SYSTEM_VERSION);
		$result3 = $GLOBALS['connection']->Execute($sql);
		if (!$result3) {
			$error_message .= "<li>".$GLOBALS['connection']->ErrorMsg()."</li>";
		}

		echo "<li>Set default values: ";
		if (!$result1 || !$result2 || !$result3) {
			echo '<font color="red"><b>FAILED</b></font></li>';
			$message .= "<li>Can't set default values. Does table created successfully?</li>";
		} else {
			echo '<font color="green"><b>PASSED!</b></font></li>';
		}
	}

	if ($error_message == "") {
		$GLOBALS['connection']->CompleteTrans();
	} else {
		$GLOBALS['connection']->RollbackTrans();
	}
	
	echo "</ul>";
	if ($error_message != "") {
		echo "Failed to create tables. Please contact your host and request that they:";
		echo "<ul>";
		echo $message;
		echo "</ul>";
		echo "Database error messages:";
		echo "<ul>";
		echo $error_message;
		echo "</ul>";
		return -1;
	} else {
		return 0;
	}
}

$error = CheckTables();
?>

