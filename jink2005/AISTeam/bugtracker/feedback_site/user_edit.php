<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_edit.php,v 1.10 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");

AuthCheckAndLogin();

$sql = "select * from ".$GLOBALS['BR_customer_user_table']." where customer_user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_uid']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "user");
}

$realname = $result->fields["realname"];
$email = $result->fields["email"];
$customer_id = $result->fields["customer_id"];
$language = $result->fields["language"];
$created_date = $result->fields["created_date"];
$last_login = $result->fields["last_login"];
$auto_cc_to = $result->fields["auto_cc_to"];

$sql = "select * from ".$GLOBALS['BR_customer_table']." where customer_id=".$GLOBALS['connection']->QMagic($customer_id);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer");
}
$customer_name = $result->fields["customer_name"];

?>
<script language="JavaScript">
<!--
function check1()
{
	var f=document.form1;
	var y='';
	if (!f.password1.value || !f.password2.value) {
		y = '<?php echo addslashes(str_replace("@key@", $STRING['password'], $STRING['no_empty']));?>';
		alert(y);
		return false;
	}
	if (!((f.password1.value == '12345678') && (f.password2.value == 'AlexWang'))) {
		if (f.password1.value!=f.password2.value) {
			y+='\n<?php echo addslashes($STRING['password_not_match'])?>';
			alert(y);
			return false;
		}
	}
	
	return OnSubmit(f);
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['my_account']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="images/outline_user.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['my_account']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="system.php"><img src="images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3><?php echo $customer_name?></h3>
			<form method="POST" action="user_doedit.php" onsubmit="return check1();" name="form1">
			<input type="hidden" name="user_id" value="<?php echo $_GET['user_id']?>">
			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>

				<table class="table-input-form">
				<tr>
					<td width="250" class="item_prompt_small">
						<?php echo $STRING['real_name'].$STRING['colon']?>
					</td>
					<td width="250">
						<input class="input-form-text-field" type="text" name="realname" size="30" value="<?php echo $realname?>" maxlength="100">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small"><?php echo $STRING['email'].$STRING['colon']?></td>
					<td><?php echo $email?></td>
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
<?php
	if ($customer_id != 0) {
?>

				<tr>
					<td class="item_prompt_small"><?php echo $STRING['auto_cc_to'].$STRING['colon']?></td>
					<td>
<?php
	if ($auto_cc_to == 'f') {
		echo $STRING['no'];
	}else{
		echo $STRING['yes'];
	}
?>
					</td>
				</tr>
<?php
}
?>
				</table>
			</fieldset>

			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>


<?php

include("include/tail.php");
?>
