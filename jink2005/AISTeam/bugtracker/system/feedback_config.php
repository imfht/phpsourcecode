<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_config.php,v 1.11 2008/11/30 03:46:29 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$sql = "select * from ".$GLOBALS['BR_feedback_config_table'];
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	DBError(__FILE__.":".__LINE__);
}

$row = $result->FetchRow();

?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f = document.form1;
	var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
	if(!f.feedback_system_name.value){y+='-<?php echo addslashes($STRING['feedback_system_name'])?><br>';}
	if(!f.mail_from_name.value){y+='-<?php echo addslashes($STRING['mail_from_name'])?><br>';}
	if(!f.mail_from_email.value){y+='-<?php echo addslashes($STRING['mail_from_email'])?><br>';}
	if(!f.import_description.value){y+='-<?php echo addslashes($STRING['import_description'])?><br>';}
	if(!f.closed_description.value){y+='-<?php echo addslashes($STRING['closed_description'])?><br>';}
	if(y=='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>'){
		return OnSubmit(f);
	}else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['feedback_system'])?>',
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
	<a href="system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['feedback_system']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_feedback_config.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['feedback_system']?></tt>
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

			<form method="POST" action="feedback_doconfig.php" onsubmit="return check1();" name="form1">
			<fieldset>
				<legend><?php echo $STRING['basic_config']?></legend>

				<table class="table-input-form">

				<tr>
					<td class="item_prompt_small" width="33%">
						<?php echo $STRING['feedback_system_name'].$STRING['colon']?>
					</td>
					<td width="67%">
						<input type="text" class="input-form-text-field" size="35" name="feedback_system_name" value="<?php echo $row["feedback_system_name"]?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['mail_from_name'].$STRING['colon']?>
					</td>
					<td>
						<input type="text" class="input-form-text-field" size="35" name="mail_from_name" value="<?php echo $row['mail_from_name']?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['mail_from_email'].$STRING['colon']?>
					</td>
					<td>
						<input type="text" class="input-form-text-field" size="35" name="mail_from_email" value="<?php echo $row['mail_from_email']?>" maxlength="60">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
<?php
echo $STRING['login_mode'].$STRING['colon'];
PrintTip($STRING['hint_title'], $STRING['login_mode_hint']);
?>

					</td>
					<td>
						<select name="login_mode">
<?php
$mode = array("mode_disabled", "mode_customer", "mode_anonymous", "mode_both");
for ($i = 0; $i < sizeof($mode); $i++) {
	$selected = "";
	if ($mode[$i] == $row["login_mode"]) {
		$selected = "selected";
	}
	echo '
							<option value="'.$mode[$i].'" '.$selected.'>'.$STRING[$mode[$i]].'</option>';
}
?>

						</select>
					</td>
				</tr>
				</table>
			</fieldset>

			<table class="table-input-form">
				<tr>
					<td class="item_prompt_small prompt_align_top" width="33%">
<?php
echo $STRING['import_description'].$STRING['colon'];
PrintTip($STRING['hint_title'], $STRING['import_description_hint']);
?>
					
					</td>
					<td width="100%">
						<textarea class="input-form-text-textarea" name="import_description" rows="10" cols="40" style="width:95%"><?php echo $row['import_description']?></textarea>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small prompt_align_top">
<?php
echo $STRING['closed_description'].$STRING['colon'];
PrintTip($STRING['hint_title'], $STRING['closed_description_hint']);
?>
					
					</td>
					<td>
						<textarea class="input-form-text-textarea" name="closed_description" rows="10" cols="40" style="width:95%"><?php echo $row['closed_description']?></textarea>
					</td>
				</tr>
			</table>
			<p align="center">
				<input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button">
				<input type="reset" value="<?php echo $STRING['button_reset']?>" name="B2" class="button">
			</p>
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php  

include("../include/tail.php");
?>
