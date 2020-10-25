<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_user_edit.php,v 1.14 2013/06/29 08:34:29 alex Exp $
 *
 */
include("../include/header.php");
include("../include/customer_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['customer_id'])) {
	$_GET['customer_id'] = $_SESSION[SESSION_PREFIX.'back_array']['customer_id'];
}

if (!$_GET['customer_user_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_user_id");
}
	
$sql = "select * from ".$GLOBALS['BR_customer_user_table']." where customer_user_id=".$GLOBALS['connection']->QMagic($_GET['customer_user_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer_user");
}

$realname = $result->fields["realname"];
$email = $result->fields["email"];
$customer_id = $result->fields["customer_id"];
$language = $result->fields["language"];
$created_date = $result->fields["created_date"];
$last_login = $result->fields["last_login"];
$account_disabled = $result->fields["account_disabled"];
$auto_cc_to = $result->fields["auto_cc_to"];

$customer_array = GetAllCustomers();
$customer_name = GetCustomerNameFromID($customer_array, $customer_id);
?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.form1;
	var y='';
	if (!f.email.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_customer_user'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['email'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	if (!f.password1.value || !f.password2.value) {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_customer_user'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['password'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	if (!((f.password1.value == '12345678') && (f.password2.value == 'AlexWang'))) {
		if (f.password1.value!=f.password2.value) {
			ALEXWANG.Dialog.Show({
				title: '<?php echo addslashes($STRING['edit_customer_user'])?>',
				msg: '<?php echo addslashes($STRING['password_not_match']);?>',
				width: 300,
				buttons: ['ok']
			});
			return false;
		}
	}
	
	return OnSubmit(f);
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="customer_admin.php"><?php echo $STRING['customer_management']?></a> /
	<a href="customer_user_admin.php?customer_id=<?php echo $customer_id?>"><?php echo $customer_name?></a> /
	<?php echo $STRING['edit_customer_user']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_customer_user.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['edit_customer_user']?></tt></td>
			<td nowrap width="100%" align="center" valign="bottom"></td>
			<td nowrap valign="bottom">
				<a href="customer_user_admin.php?customer_id=<?php echo $customer_id?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3><?php echo $STRING['edit_customer_user']?></h3>
			<form method="POST" action="customer_user_doedit.php" onsubmit="return check1();" name="form1">
			<input type="hidden" name="customer_user_id" value="<?php echo $_GET['customer_user_id']?>">
			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>
				<table class="table-input-form">
				<tr>
					<td width="33%" class="item_prompt_small"><?php echo $STRING['real_name'].$STRING['colon']?></td>
					<td width="67%">
						<input class="input-form-text-field" type="text" name="realname" size="30" value="<?php echo $realname?>" maxlength="100">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['customer_name'].$STRING['colon']?></td>
					<td>
						<select name="customer_id" size="1">
<?php
for ($i = 0; $i < sizeof($customer_array); $i++) {
	if ($customer_id == $customer_array[$i]->getcustomerid()) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
							<option value="'.$customer_array[$i]->getcustomerid().'" '.$selected.'>'.$customer_array[$i]->getcustomername().'</option>
		';
}
?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['email'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" type="text" name="email" size="30" value="<?php echo $email?>" maxlength="50"></td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['password'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" name="password1" size="30" type="password" value="12345678" maxlength="50"></td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['verify_password'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" name="password2" size="30" type="password" value="AlexWang" maxlength="50"></td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['language'].$STRING['colon']?></td>
					<td>
						<select size="1" name="language">
<?php
$get_language_sql = "select * from ".$GLOBALS['BR_language_table']." order by language_desc";
$get_language_result = $GLOBALS['connection']->Execute($get_language_sql) or DBError(__FILE__.":".__LINE__);
while ($lang_row = $get_language_result->FetchRow()) {
	$this_language = $lang_row["language"];
	$this_language_desc = $lang_row["language_desc"];
	if ($this_language == $language) {
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
if ($account_disabled == 't') {
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
	if ($customer_id != 0) {
?>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['auto_cc_to'].$STRING['colon']?></td>
					<td>
<?php
if ($auto_cc_to == 'f') {
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
			<p align="center">
			<input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>


<?php

include("../include/tail.php");
?>