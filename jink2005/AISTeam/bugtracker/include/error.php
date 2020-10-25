<?php
/* Copyright(c) 2003-2007 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: error.php,v 1.18 2013/07/07 21:31:13 alex Exp $
 *
 */
function MessageBoxPrint($error, $message, $ButtonInput)
{
	global $STRING;

	if ($error) {
		$hint_image = '<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/error.gif" width="70" height="70">';
		$hint = '<p>'.$STRING['error_hint'].'</p><p>'.$STRING['return_try'].'</p>';
		$mesg_title = $STRING['msg_title_oops'];
		$message = '<p align="center">'.$STRING['error_title'].'</p>'.$message;
	} else {
		$hint_image = '<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/config.png" width="80" height="76">';
		$hint = $STRING['finish_mesg'];
		$mesg_title = $STRING['msg_title_success'];
	}
?>
<div id="report_mesg_main">
	<div class="report_mesg_sub">
		<div class="mesg_left">
			<p align="center"><?php echo $hint_image?><p><?php echo $hint?>
		</div>
		<div class="mesg_right mesg_right1">
			<?php echo $mesg_title?>
		</div>
		<div class="mesg_right mesg_right2">
			<table width="100%" height="100%">
			<tr>
				<td>
					<?php echo $message?>
				</td>
			</tr>
			</table>
		</div>
		<div class="mesg_right mesg_right3">
			<?php echo $ButtonInput?>
		</div>
	</div>
</div>
<?php
}

/* The error_key is the string key of the error message.
 *
 * Te arg_key is the string_key in the error message arg.
 * For example, if the error message is:
 * "The @key@ should be less than @string@ "
 * The arg_key is the string key of @key@ and 
 * the arg_string is will replace the @string@.
 */
function ErrorPrintOut($error_key, $arg_key = "", $arg_string = "")
{
	global $STRING;

	$message = $STRING[$error_key];
	if ($message == "") {
		$message = $error_key;
	}
	if ($arg_key != "") {
		$arg_value = $STRING[$arg_key];
		$message = str_replace("@key@", $arg_value, $message);
	}
	if ($arg_string != "") {
		$message = str_replace("@string@", $arg_string, $message);
	}

	// Return to index or back to previous page.
	if ($error_key == "timeout") {
		$ButtonInput = '<input class="button" type=button value="'.$STRING['back'].'" onclick="Redirect(\''.$GLOBALS['SYS_URL_ROOT'].'/index.php\');">';
	} else {
		$ButtonInput = '<input class="button" type=button value="'.$STRING['back'].'" onclick="history.go( -1 );return true;">';
	}
	
	MessageBoxPrint(true, $message, $ButtonInput);

	include($GLOBALS["SYS_PROJECT_PATH"]."/include/tail.php");
	exit;
}

function ErrorPrintBackFormOut($method, $action, $args_list, 
							   $error_key, $arg_key = "", $arg_string = "")
{
	global $STRING;

	$message = $STRING[$error_key];
	if ($message == "") {
		$message = $error_key;
	}
	if ($arg_key != "") {
		$arg_value = $STRING[$arg_key];
		$message = str_replace("@key@", $arg_value, $message);
	}

	if ($arg_string != "") {
		$message = str_replace("@string@", $arg_string, $message);
	}

	$args = array_keys($args_list);
	$back_array = array();
	for ($i = 0; $i < sizeof($args); $i++) {
		$name = $args[$i];
		$value = $args_list[$args[$i]];
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		$the_array = array($name=>$value);
		$back_array = array_merge($back_array, $the_array);
	}
	$_SESSION[SESSION_PREFIX.'back_array'] = $back_array;

	echo "<form method=\"$method\" action=\"$action\">";
	$query_string = strstr($action, '?');
	$query_string = substr($query_string, 1);
	$pair = explode("&", $query_string);
	for ($i = 0; $i < sizeof($pair); $i++) {
		list($key, $value) = explode("=", $pair[$i], 2);
		echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
	}
	echo '<input type="hidden" name="error_back" value="1">';

	MessageBoxPrint(true, $message, '<input class="button" type=submit value="'.$STRING['back'].'">');
	
	echo '</form>';
	
	include($GLOBALS["SYS_PROJECT_PATH"]."/include/tail.php");
	exit;
}

function FinishPrintOut($return_url, $mesg_key, $arg_key = "", $exit = 1)
{
	global $STRING;
	global $SYSTEM;

	$message = $STRING[$mesg_key];
	if ($message == "") {
		$message = $mesg_key;
	}
	if ($arg_key != "") {
		$arg_value = $STRING[$arg_key];
		$message = str_replace("@key@", $arg_value, $message);
	}
	$pos = strpos($return_url, "?");
	if ($pos != FALSE) {
		$url = substr($return_url, 0, $pos);
		$args = substr($return_url, $pos+1);
		$arg_array = explode("&", $args);
	} else {
		$url = $return_url;
	}
	
	echo '<form action="'.$url.'" name="form1" method="GET">';

	for ($i = 0; $i<sizeof($arg_array); $i++) {
		list($name, $value) = explode("=", $arg_array[$i]);
		echo '<input type="hidden" name="'.$name.'" value="'.rawurldecode($value).'">';
	}

	MessageBoxPrint(false, $message, '<input class="button" type="submit" value="'.$STRING['continue'].'">');

	echo '</form>';
	if ($SYSTEM['auto_redirect'] == 't') {
		echo "<script>";
		echo "document.form1.submit();";
		echo "</script>";
	}
	include($GLOBALS["SYS_PROJECT_PATH"]."/include/tail.php");

	if ($exit) {
		exit;
	}
	
}

function WriteSyslog($log_type, $log_string_key, $arg_key = "", $arg_string = "")
{
	global $SYSTEM;

	if ($log_type == "error") {
		$log_type = 3;
	} else if ($log_type == "warn") {
		$log_type = 2;
	} else {
		$log_type = 1; // info
	}
	if (!isset($_SESSION[SESSION_PREFIX.'uid'])) {
		$user_id = 0;
	} else {
		$user_id = $_SESSION[SESSION_PREFIX.'uid'];
	}
	// Avoid path with non-unicode
	$slash = strrchr($arg_string, "/");
	if ($slash) {
		$arg_string = utf8_substr($slash, 1);
	}
	if (utf8_strlen($arg_string) > 128) {
		$arg_string = utf8_substr($arg_string, 0, 127);
	}

	$now = $GLOBALS['connection']->DBTimeStamp(time());
	$sql = "insert into ".$GLOBALS['BR_syslog_table']."(user_id, ip, log_type,
			time, log_string_key, arg_key, arg_string) values($user_id, 
			".$GLOBALS['connection']->QMagic($_SERVER['REMOTE_ADDR']).", $log_type,
			$now, ".$GLOBALS['connection']->QMagic($log_string_key).",
			".$GLOBALS['connection']->QMagic($arg_key).", 
			".$GLOBALS['connection']->QMagic($arg_string).")";

	$GLOBALS['connection']->Execute($sql) or die("Failed to do syslog.");
	$syslog_id = $GLOBALS['connection']->Insert_ID($GLOBALS['BR_syslog_table'], 'syslog_id');;

	if ($SYSTEM['max_syslog'] > (2147483647-1)) {
		$SYSTEM['max_syslog'] = 100000;
	} else if (!$SYSTEM['max_syslog']) {
		$SYSTEM['max_syslog'] = 1000;
	}
	if ($SYSTEM['max_syslog'] > 20) {
		$flex = floor($SYSTEM['max_syslog']/10);
	} else {
		$flex = 1;
	}

	if ($syslog_id % $flex == 0) {
		$count_sql = "SELECT count(syslog_id) FROM ".$GLOBALS['BR_syslog_table'];
		$count_result = $GLOBALS['connection']->Execute($count_sql) or die("Failed to rotate syslog");
		$count = $count_result->fields[0];

		if ($count > $SYSTEM['max_syslog']) {
			$start = $count - $SYSTEM['max_syslog'] + $flex;

			$sql = "SELECT syslog_id FROM ".$GLOBALS['BR_syslog_table']." ORDER BY syslog_id ASC";
			$result = $GLOBALS['connection']->SelectLimit($sql, 1, $start);
			$remove_less_than = $result->fields[0];
			$sql = "DELETE from ".$GLOBALS['BR_syslog_table']." where syslog_id < ".$remove_less_than;
			$GLOBALS['connection']->Execute($sql) or die("Failed to rotate syslog.");

		}
	}	
}

function DBError($string)
{
	echo "<p><b>".$GLOBALS['connection']->ErrorMsg()."</b></p>";

	// To stop transaction, so the WriteSyslog can do syslog out of transaction.
	$GLOBALS['connection']->RollbackTrans();
	$GLOBALS['connection']->Close();
	$GLOBALS['connection']->Connect($GLOBALS['BR_dbserver'], $GLOBALS['BR_dbuser'], $GLOBALS['BR_dbpwd'], $GLOBALS['BR_dbname']);

	WriteSyslog("error", "db_error", "", $string);
	ErrorPrintOut("db_error", "", $string);
}
?>
