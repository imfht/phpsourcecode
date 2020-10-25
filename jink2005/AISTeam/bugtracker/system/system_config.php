<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: system_config.php,v 1.15 2008/11/30 03:46:29 alex Exp $
 *
 */
include("../include/header.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

?>
<script language="JavaScript" type="text/javascript">
<!--
function CheckImap()
{
	var f = document.form1;

	if (f.auth_method[0].checked) {
		f.imap_server.disabled = true;
		f.imap_port.disabled = true;
	} else {
		f.imap_server.disabled = false;
		f.imap_port.disabled = false;
	}
}
function CheckSMTP()
{
	var f = document.form1;

	if (f.mail_function[2].checked) {
		f.mail_smtp_server.disabled = false;
		f.mail_smtp_port.disabled = false;
		f.mail_smtp_auth.disabled = false;
		if (f.mail_smtp_auth.checked) {
			f.mail_smtp_user.disabled = false;
			f.mail_smtp_password.disabled = false;
			f.mail_verify_password.disabled = false;
		} else {
			f.mail_smtp_user.disabled = true;
			f.mail_smtp_password.disabled = true;
			f.mail_verify_password.disabled = true;
		}
	} else {
		f.mail_smtp_server.disabled = true;
		f.mail_smtp_port.disabled = true;
		f.mail_smtp_auth.disabled = true;
		f.mail_smtp_user.disabled = true;
		f.mail_smtp_password.disabled = true;
		f.mail_verify_password.disabled = true;
	}
}
function check1()
{
	var f = document.form1;
	var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
	if(!f.program_name.value){y+='-<?php echo addslashes($STRING['program_name'])?><br>';}
	if(!f.mail_from_name.value){y+='-<?php echo addslashes($STRING['mail_from_name'])?><br>';}
	if(!f.mail_from_email.value){y+='-<?php echo addslashes($STRING['mail_from_email'])?><br>';}
	if (f.auth_method[1].checked) {
		if (!f.imap_server.value) {
			y+='-<?php echo addslashes($STRING['imap_server'])?><br>';
		}
		if (!f.imap_port.value) {
			y+='-<?php echo addslashes($STRING['imap_port'])?><br>';
		}
	}
	if (f.mail_function[2].checked) {
		if (!f.mail_smtp_server.value) {
			y+='-<?php echo addslashes($STRING['mail_smtp_server'])?><br>';
		}
		if (!f.mail_smtp_port.value) {
			y+='-<?php echo addslashes($STRING['mail_smtp_port'])?><br>';
		}
		if (f.mail_smtp_auth.checked) {
			if (!f.mail_smtp_user.value) {
				y+='-<?php echo addslashes($STRING['mail_smtp_user'])?><br>';
			}
			if (!((f.mail_smtp_password.value == '12345678') && (f.mail_verify_password.value == 'AlexWang'))) {
				if(f.mail_smtp_password.value!=f.mail_verify_password.value) {y+='<br><?php echo addslashes($STRING['password_not_match'])?>';}
			}
		}
	}
	if(y=='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>'){
		return OnSubmit(f);
	}else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['system_config'])?>',
			msg: y,
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php"><?php echo $STRING['title_system']?></a> / <?php echo $STRING['system_config']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_system_config.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['system_config']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="system.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>

			<form method="POST" action="system_doconfig.php" onsubmit="return check1();" name="form1">
			<fieldset>
				<legend><?php echo $STRING['basic_config']?></legend>

				<table class="table-input-form">
				<tr>
					<td class="item_prompt_small" width="35%">
						<?php echo $STRING['program_name'].$STRING['colon']?>
					</td>
					<td width="65%">
						<input class="input-form-text-field" type="text" size="35" name="program_name" value="<?php echo $SYSTEM['program_name']?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['date_format'].$STRING['colon']?>
					</td>
					<td>
						<select name="date_format" size="1">
<?php
foreach ($GLOBALS['SYS_DATE_FORMAT'] as $key => $value) {
	if ($key == $SYSTEM['date_format']) {
		echo '
							<option value="'.$key.'" selected>'.date($key).' ('.$value.')</option>';
	} else {
		echo '
							<option value="'.$key.'">'.date($key).' ('.$value.')</option>';
	}
}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['auto_redirect'].$STRING['colon']?>
					</td>
					<td>
<?php
if ($SYSTEM['auto_redirect'] == 't') {
	echo '
						<input type="radio" name="auto_redirect" value="t" checked>'.$STRING['yes'].'&nbsp
						<input type="radio" name="auto_redirect" value="f">'.$STRING['no'];
} else {
	echo '
						<input type="radio" name="auto_redirect" value="t">'.$STRING['yes'].'&nbsp
						<input type="radio" name="auto_redirect" value="f" checked>'.$STRING['no'];
}
?>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small prompt_align_top">
						<?php echo $STRING['auth_method'].$STRING['colon']?>
					</td>
					<td>
<?php
if ($SYSTEM['auth_method'] == "native") {
	$native_checked = "checked";
	$imap_checked = "";
} else {
	$native_checked = "";
	$imap_checked = "checked";
}
?>
						<input type="radio" class="checkbox" name="auth_method" value="native" onClick="CheckImap()" <?php echo $native_checked?> ><?php echo $STRING['auth_native']?><br>
						<input type="radio" class="checkbox" name="auth_method" value="imap" onClick="CheckImap()" <?php echo $imap_checked?> ><?php echo $STRING['auth_imap']?><br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $STRING['imap_server'].$STRING['colon']?>
						<input class="input-form-text-field" type="text" name="imap_server" value="<?php echo $SYSTEM['imap_server']?>" maxlength="256" size="20"><br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $STRING['imap_port'].$STRING['colon']?>
						<input class="input-form-text-field" type="text" name="imap_port" value="<?php echo $SYSTEM['imap_port']?>" maxlength="5" size="20">
					</td>
				</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend><?php echo $STRING['mail_config']?></legend>
	
				<table class="table-input-form">
				<tr>
					<td width="35%" class="item_prompt_small">
						<?php echo $STRING['mail_from_name'].$STRING['colon']?>
					</td>
					<td width="65%">
						<input class="input-form-text-field" type="text" size="35" name="mail_from_name" value="<?php echo $SYSTEM['mail_from_name']?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['mail_from_email'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" type="text" size="35" name="mail_from_email" value="<?php echo $SYSTEM['mail_from_email']?>" maxlength="60">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['allow_subscribe'].$STRING['colon']?>
					</td>
					<td>
<?php
if ($SYSTEM['allow_subscribe'] == 't') {
	echo '
					<input type="radio" name="allow_subscribe" value="t" checked>'.$STRING['yes'].'
					<input type="radio" name="allow_subscribe" value="f">'.$STRING['no'];
} else {
	echo '
					<input type="radio" name="allow_subscribe" value="t">'.$STRING['yes'].'
					<input type="radio" name="allow_subscribe" value="f" checked>'.$STRING['no'];
}
?>

					</td>
				</tr>
				<tr>
					<td class="item_prompt_small prompt_align_top">
						<?php echo $STRING['mail_function'].$STRING['colon']?>
						<?php echo PrintTip($STRING['hint_title'], $STRING['mail_function_hint'])?>
					</td>
					<td>
<?php
if ($SYSTEM['mail_function'] == "nosend") {
	$send_mail_checked = "checked";
	$mail_checked = "";
	$smtp_checked = "";
} else if ($SYSTEM['mail_function'] == "smtp") {
	$send_mail_checked = "";
	$mail_checked = "";
	$smtp_checked = "checked";
} else {
	$send_mail_checked = "";
	$mail_checked = "checked";
	$smtp_checked = "";
}
if ($SYSTEM['mail_smtp_auth'] == "t") {
	$smtp_auth_checked = "checked";
}
?>
						<input type="radio" class="checkbox" name="mail_function" value="nosend" onClick="CheckSMTP()" <?php echo $send_mail_checked?>><?php echo $STRING['mail_function_nosendmail']?><br>
						<input type="radio" class="checkbox" name="mail_function" value="mail" onClick="CheckSMTP()" <?php echo $mail_checked?>><?php echo $STRING['mail_function_sendmail']?><br>
						<input type="radio" class="checkbox" name="mail_function" value="smtp" onClick="CheckSMTP()" <?php echo $smtp_checked?>><?php echo $STRING['mail_function_phpsmtp']?>
						<table border="0" width="92%" align="right">
						<tr>
							<td width="47%"><?php echo $STRING['mail_smtp_server'].$STRING['colon']?></td>
							<td><input class="input-form-text-field" type="text" name="mail_smtp_server" value="<?php echo $SYSTEM['mail_smtp_server']?>" maxlength="255" size="20"></td>
						</tr>
						<tr>
							<td><?php echo $STRING['mail_smtp_port'].$STRING['colon']?></td>
							<td><input class="input-form-text-field" type="text" name="mail_smtp_port" value="<?php echo $SYSTEM['mail_smtp_port']?>" maxlength="5" size="20"></td>
						</tr>
						<tr>
							<td colspan="2"><input type="checkbox" class="checkbox" name="mail_smtp_auth" value="t" onClick="CheckSMTP()" <?php echo $smtp_auth_checked?>><?php echo $STRING['mail_smtp_auth']?></td>
						</tr>
						<tr>
							<td><?php echo $STRING['mail_smtp_user'].$STRING['colon']?></td>
							<td><input class="input-form-text-field" type="text" name="mail_smtp_user" value="<?php echo $SYSTEM['mail_smtp_user']?>" maxlength="128" size="20"></td>
						</tr>
						<tr>
							<td><?php echo $STRING['mail_smtp_password'].$STRING['colon']?></td>
							<td><input class="input-form-text-field" type="password" name="mail_smtp_password" value="12345678" maxlength="128" size="20"></td>
						</tr>
						<tr>
							<td><?php echo $STRING['verify_password'].$STRING['colon']?></td>
							<td><input class="input-form-text-field" type="password" name="mail_verify_password" value="AlexWang" maxlength="30" size="20"></td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</fieldset>
	
			<fieldset>
				<legend><?php echo $STRING['limit_config']?></legend>
	
				<table class="table-input-form">
				<tr>
					<td class="item_prompt_small" width="35%">
						<?php echo $STRING['max_area'].$STRING['colon']?>
					</td>
					<td width="65%">
						<select size="1" name="max_area">
<?php
$num_array = array(3, 5, 8, 10, 12, 15, 20, 30, 40, 50);	
for ($i = 0; $i < sizeof($num_array); $i++) {
	if ($num_array[$i] == $SYSTEM['max_area']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$num_array[$i].'" '.$selected.'>'.$num_array[$i].'</option>';
}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['max_minor_area'].$STRING['question_mark']?>
					</td>
					<td>
						<select size="1" name="max_minor_area">
<?php
for ($i = 0; $i < sizeof($num_array); $i++) {
	if ($num_array[$i] == $SYSTEM['max_minor_area']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
					<option value="'.$num_array[$i].'" '.$selected.'>'.$num_array[$i].'</option>';
}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['max_filter_per_user'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="max_filter_per_user">
<?php
$num_array = array(2, 5, 8, 10, 15, 20, 30, 40, 50);	
for ($i = 0; $i < sizeof($num_array); $i++) {
	if ($num_array[$i] == $SYSTEM['max_filter_per_user']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$num_array[$i].'" '.$selected.'>'.$num_array[$i].'</option>';
}
?>

						</select>  
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['max_shared_filter'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="max_shared_filter">
<?php
$num_array = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20);	
for ($i = 0; $i < sizeof($num_array); $i++) {
	if ($num_array[$i] == $SYSTEM['max_shared_filter']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$num_array[$i].'" '.$selected.'>'.$num_array[$i].'</option>';
}
?>

						</select>  
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['max_syslog'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="max_syslog">
<?php
$num_array = array(100, 200, 300, 500, 1000, 2000, 3000, 5000, 10000);	
for ($i = 0; $i < sizeof($num_array); $i++) {
	if ($num_array[$i] == $SYSTEM['max_syslog']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$num_array[$i].'" '.$selected.'>'.$num_array[$i].'</option>';
}
?>

						</select>  
					</td>
				</tr>
				</table>
			</fieldset>
			<p align="center">
				<input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button">
				<input type="reset" value="<?php echo $STRING['button_reset']?>" name="B2" class="button">
			</p>
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<script language="JavaScript" type="text/javascript">
<!--
CheckImap();
CheckSMTP();
-->
</script>

<?php  

include("../include/tail.php");
?>
