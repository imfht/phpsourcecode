<?php
function SetupDatabase()
{
	error_reporting(0);
	include("../include/config.php");
	include($GLOBALS["SYS_PROJECT_PATH"]."/adodb5/adodb.inc.php");

	$error_message = "";
	$warn_message = "";
	$need_to_createdb = FALSE;

	$GLOBALS['connection'] = &ADONewConnection($GLOBALS['BR_dbtype']);
	$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], $GLOBALS['BR_dbpwd'], $GLOBALS['BR_dbname']);
	if (!$GLOBALS['connection']->IsConnected()) {
		$need_to_createdb = TRUE;
	}

	$db_error_message = "";
	if ($need_to_createdb) {
		if ($GLOBALS['BR_dbtype'] == "postgres") {
			// Connect to default database
			$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], 
											$GLOBALS['BR_dbpwd'], "template1") or 
				$db_error_message .= $GLOBALS['connection']->ErrorMsg();
			if ($GLOBALS['connection']->IsConnected()) {
				$sql = "create database ".$GLOBALS['BR_dbname']." WITH ENCODING = 'SQL_ASCII' TEMPLATE template0;";
				$GLOBALS['connection']->Execute($sql) or $db_error_message .= $GLOBALS['connection']->ErrorMsg();
			}

		} else if (strstr($GLOBALS['BR_dbtype'], "mysql")) {
			// Connect to default database
			$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], 
											$GLOBALS['BR_dbpwd'], "mysql") or 
				$db_error_message .= $GLOBALS['connection']->ErrorMsg();

			if ($GLOBALS['connection']->IsConnected()) {
				$sql = "create database ".$GLOBALS['BR_dbname']." DEFAULT CHARACTER SET utf8";
				$GLOBALS['connection']->Execute($sql) or 
					$GLOBALS['connection']->Execute("create database ".$GLOBALS['BR_dbname']) or 
					$db_error_message .= $GLOBALS['connection']->ErrorMsg();
			}
		}

		$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], $GLOBALS['BR_dbpwd'], $GLOBALS['BR_dbname']);
		if (!$GLOBALS['connection']->IsConnected()) {
			$db_error_message .= $GLOBALS['connection']->ErrorMsg();
		}
	}

	echo "<ul>";
	echo "<li>Connect to database ".$GLOBALS['BR_dbname'].": ";
	if ($need_to_createdb) {
		echo 'FAILED (That\'s ok. I will create it).';
	} else {
		echo '<font color=green><b>PASSED!</b></font>';
	}
	echo '</li>';

	if ($need_to_createdb) {
		echo "<li>Create new database ".$GLOBALS['BR_dbname'].": ";
		if ($db_error_message != "") {
			echo '<font color=red><b>FAILED!!</b></font>';
		} else {
			echo '<font color=green><b>PASS!!</b></font>';
		}
	}
	echo "</ul>";

	if (!$GLOBALS['connection']->IsConnected()) {
		echo "Failed to create database. Please refer to the following information and correct it:";
		echo "<ul>";
		echo "<li>".$db_error_message."</li>";
		$error = error_get_last();
		echo "<li>".$error["message"]."</li>";
		echo "</ul>";
		return -1;
	} else {
		return 0;
	}
}

$error = SetupDatabase();
	
?>

