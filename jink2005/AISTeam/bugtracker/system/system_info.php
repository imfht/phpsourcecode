<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: system_info.php,v 1.11 2008/11/28 10:36:31 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_sysinfo'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$system_info_key = array("program_name",
						 "version",
						 "date_format",
						 "auto_redirect",
						 "auth_method",
						 "imap_server",
						 "imap_port",
						 "mail_from_name",
						 "mail_from_email",
						 "mail_function",
						 "mail_smtp_server",
						 "mail_smtp_port",
						 "mail_smtp_auth",
						 "mail_smtp_user",
						 "mail_smtp_password",
						 "allow_subscribe",
						 "max_area",
						 "max_minor_area",
						 "max_filter_per_user",
						 "max_shared_filter",
						 "max_syslog");

$system_info = $SYSTEM;
$system_info["date_format"] = date($SYSTEM["date_format"])." (".$system_info["date_format"].")";
if ($system_info["auto_redirect"] == 't') {
	$system_info["auto_redirect"] = $STRING["yes"];
} else {
	$system_info["auto_redirect"] = $STRING["no"];
}
if ($system_info["auth_method"] == 'native') {
	$system_info["auth_method"] = $STRING["auth_native"];
} else {
	$system_info["auth_method"] = $STRING["auth_imap"];
}

if ($system_info["mail_function"] == 'nosend') {
	$system_info["mail_function"] = $STRING["mail_function_nosendmail"];
} else if ($system_info["mail_function"] == 'smtp') {
	$system_info["mail_function"] = $STRING["mail_function_phpsmtp"];
} else {
	$system_info["mail_function"] = $STRING["mail_function_sendmail"];
}

$system_info["mail_smtp_password"] = "********";

if ($system_info["mail_smtp_auth"] == 't') {
	$system_info["mail_smtp_auth"] = $STRING["yes"];
} else {
	$system_info["mail_smtp_auth"] = $STRING["no"];
}
if ($system_info["allow_subscribe"] == 't') {
	$system_info["allow_subscribe"] = $STRING["yes"];
} else {
	$system_info["allow_subscribe"] = $STRING["no"];
}

$sql = "select * from ".$GLOBALS['BR_feedback_config_table'];
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$feedback_info = $result->FetchRow();
$feedback_info['login_mode'] = $STRING[$feedback_info['login_mode']];

$sql = "select count(*) from ".$GLOBALS['BR_user_table'];
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$count = $result->fields[0];
$other_info["count_user"] = $count;
   
$sql = "select count(*) from ".$GLOBALS['BR_customer_user_table'];
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$count = $result->fields[0];
$other_info["count_customer_user"] = $count;

?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php?page=information"><?php echo $STRING['title_information']?></a> /
	<?php echo $STRING['system_info']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_system_info.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['system_info']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="system.php?page=information"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>

			<fieldset>
				<legend><?php echo $STRING['system_config']?></legend>

				<table class="table-input-form">

<?php
for ($i = 0; $i < sizeof($system_info_key); $i++) {
	if ($STRING[$system_info_key[$i]] == "") {
		// Skip the 0, 1, 2....
		continue;
	}
	echo '
				<tr>
					<td class="item_prompt_small" width="350">
						'.$STRING[$system_info_key[$i]].$STRING['colon'].'
					</td>
					<td width="350">
						'.$system_info[$system_info_key[$i]].'
					</td>
				</tr>';
}
?>

				</table>
			</fieldset>
	
			<fieldset>
				<legend><?php echo $STRING['feedback_system']?></legend>

				<table class="table-input-form">
<?php
$key = array_keys($feedback_info);
for ($i = 0; $i < sizeof($key); $i++) {
	$field = $key[$i];
	if ($STRING[$field] == "") {
		// Skip the 0, 1, 2....
		continue;
	}

	echo '
				<tr>
					<td class="item_prompt_small" width="350">
						'.$STRING[$field].$STRING['colon'].'
					</td>
					<td width="350">
						'.$feedback_info[$field].'
					</td>
				</tr>';

}
?>
				</table>
			</fieldset>

			<fieldset>
				<legend><?php echo $STRING['system_usage']?></legend>

				<table class="table-input-form">
<?php
$key = array_keys($other_info);
for ($i = 0; $i < sizeof($key); $i++) {
	$field = $key[$i];
	echo '
				<tr>
					<td class="item_prompt_small" width="350">
						'.$STRING[$field].$STRING['colon'].'
					</td>
					<td width="350">
						'.$other_info[$field].'
					</td>
				</tr>';
}
?>

				</table>
            </fieldset>
			<p></p>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php  

include("../include/tail.php");
?>
