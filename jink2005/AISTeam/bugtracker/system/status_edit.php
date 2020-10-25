<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: status_edit.php,v 1.13 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_status'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!$_GET['status_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "status_id");
}
$sql = "select * from ".$GLOBALS['BR_status_table']." where status_id=".$GLOBALS['connection']->QMagic($_GET['status_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "status", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "status");
}
$status_name = $result->fields["status_name"];
$status_color = $result->fields["status_color"];
$status_type = $result->fields["status_type"];

?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.form1;

	if(!f.status_name.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_status'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['status_name'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	if(!f.status_color.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_status'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['color'], $STRING['no_empty']));?>',
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
	<a href="system.php"><?php echo $STRING['title_system']?></a> /
	<a href="status_admin.php"><?php echo $STRING['status_management']?></a> /
	<?php echo $STRING['edit_status']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_status.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['edit_status']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="status_admin.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

			<form method="POST" action="status_doedit.php" onsubmit="return check1();" name="form1">
			<input type="hidden" name="status_id" value="<?php echo $_GET['status_id']?>">

			<fieldset>
				<legend><?php echo $STRING['edit_status']?></legend>

				<table class="table-input-form">
				<tr>
					<td width="120" class="item_prompt_small">
						<?php echo $STRING['status_name'].$STRING['colon']?>
					</td>
					<td width="330">
						<input type="text" class="input-form-text-field" name="status_name" size="40" value="<?php echo $status_name?>" maxlength="60">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['color'].$STRING['colon']?>
					</td>
					<td>
						<input type="text" class="input-form-text-field" name="status_color" size="40" maxlength="20" value="<?php echo $status_color?>">
						<?php PrintTip($STRING['hint_title'], $STRING['color_hint'])?>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['type'].$STRING['colon']?>
					</td>
					<td>
						<select name="status_type" size="1">
<?php
if ($status_type == "active") {
	echo '
							<option selected value="active">'.$STRING['status_type_active'].'</option>
							<option value="closed">'.$STRING['status_type_closed'].'</option>';
} else {
	echo '
							<option value="active">'.$STRING['status_type_active'].'</option>
							<option value="closed" selected>'.$STRING['status_type_closed'].'</option>';
}
?>

						</select>
					</td>
				</tr>
				</table>
			</fieldset>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>


<?php

include("../include/tail.php");
?>
