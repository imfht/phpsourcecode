<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_user_new.php,v 1.13 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['customer_id'])) {
	$_GET['customer_id'] = $_SESSION[SESSION_PREFIX.'back_array']['customer_id'];
}

if ($_GET['customer_id'] == "") {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_id");
}
$sql = "select * from ".$GLOBALS['BR_customer_table']." where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer");
}
$customer_name = $result->fields["customer_name"];

?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.form1;
	var y='';
	if (!f.email.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_customer_user'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['email'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	if (!f.password1.value || !f.password2.value) {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_customer_user'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['password'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	if (f.password1.value!=f.password2.value) {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_customer_user'])?>',
			msg: '<?php echo addslashes($STRING['password_not_match']);?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}

	return OnSubmit(f);
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="customer_admin.php"><?php echo $STRING['customer_management']?></a> /
	<a href="customer_user_admin.php?customer_id=<?php echo $_GET['customer_id']?>"><?php echo $customer_name?></a> /
	<?php echo $STRING['new_customer_user']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_customer_user.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['new_customer_user']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom"></td>
			<td nowrap valign="bottom">
				<a href="customer_user_admin.php?customer_id=<?php echo $_GET['customer_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3><?php echo $STRING['new_customer_user']?></h3>
			<form method="POST" action="customer_user_donew.php" onsubmit="return check1();" name="form1">
			<input type="hidden" name="customer_id" value="<?php echo $_GET['customer_id']?>">
			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>
				<table class="table-input-form">
				<tr>
					<td width="33%" class="item_prompt_small"><?php echo $STRING['real_name'].$STRING['colon']?></td>
					<td width="67%">
						<input class="input-form-text-field" type="text" name="realname" size="30" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['realname']?>" maxlength="100">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['email'].$STRING['colon']?></td>
					<td>
						<input class="input-form-text-field" type="text" name="email" size="30" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['email']?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['password'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" name="password1" size="30" type="password" maxlength="50"></td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['verify_password'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" name="password2" size="30" type="password" maxlength="50"></td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['language'].$STRING['colon']?></td>
					<td>
						<select size="1" name="language">
<?php
$get_language_sql = "select * from ".$GLOBALS['BR_language_table']." order by language_desc";
$get_language_result = $GLOBALS['connection']->Execute($get_language_sql) or DBError(__FILE__.":".__LINE__);
	
if ($_SESSION[SESSION_PREFIX.'back_array']['language'] == "") {
	$_SESSION[SESSION_PREFIX.'back_array']['language'] = "en";
}
while ($lang_row = $get_language_result->FetchRow()) {
	$this_language = $lang_row["language"];
	$this_language_desc = $lang_row["language_desc"];
	if ($this_language == $_SESSION[SESSION_PREFIX.'back_array']['language']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
							<option value="'.$this_language.'" '.$selected.'>'.$this_language_desc.'</option>';
}
?>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['account_status'].$STRING['colon']?></td>
					<td>
						<select size="1" name="enable_login">
<?php
if ($_SESSION[SESSION_PREFIX.'back_array']['enable_login'] == '0') {
	echo '
							<option value="1">'.$STRING['account_enabled'].'</option>
							<option value="0" selected>'.$STRING['account_disabled'].'</option>';
}else{
	echo '
							<option value="1" selected>'.$STRING['account_enabled'].'</option>
							<option value="0">'.$STRING['account_disabled'].'</option>';
}
?>
						</select>
					</td>
				</tr>
<?php
	if ($_GET['customer_id'] != 0) {
?>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['auto_cc_to'].$STRING['colon']?></td>
					<td>
<?php
if ($_SESSION[SESSION_PREFIX.'back_array']['auto_cc_to'] == '0') {
	echo '
						<input type="radio" value="1" name="auto_cc_to" class="checkbox">'.$STRING['yes'].' &nbsp;&nbsp;
						<input type="radio" value="0" name="auto_cc_to" class="checkbox" checked>'.$STRING['no'];
}else{
	echo '
						<input type="radio" value="1" name="auto_cc_to" class="checkbox" checked>'.$STRING['yes'].' &nbsp;&nbsp;
						<input type="radio" value="0" name="auto_cc_to" class="checkbox">'.$STRING['no'];
}
?>
					</td>
				</tr>
<?php
}
?>
				</table>
			</fieldset>
				<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" name="B1" class="button"></p>
			</form>

		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>


<?php

include("../include/tail.php");
?>