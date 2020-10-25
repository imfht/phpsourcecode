<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: group_new.php,v 1.14 2008/12/13 09:27:10 alex Exp $
 *
 */
include("../include/header.php");
include("../include/status_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
    var f=document.form1;
    if(!f.group_name.value) {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_group'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['group_name'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	} else {
		return OnSubmit(f);
	}

}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="group_admin.php"><?php echo $STRING['group_management']?></a> /
	<?php echo $STRING['new_group']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_group.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['new_group']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="group_admin.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<form method="POST" action="group_donew.php" name="form1" onsubmit="return check1();">

			<table class="table-input-form" align="center">
			<tr>
				<td class="item_prompt_small">
					<?php echo $STRING['group_name'].$STRING['colon']?>
					<input class="input-form-text-field" type="text" name="group_name" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['group_name']?>" size="50" maxlength="50">
				</td>
			</tr>
			</table>

			<fieldset>
				<legend><?php echo $STRING['group_privilege']?></legend>
				<table class="table-input-form" align="center">
<?php
for ($i = 0; $i < sizeof($privilege_display_array); $i++) {
	$checked = "";
	if (isset($_SESSION[SESSION_PREFIX.'back_array'][$privilege_display_array[$i]]) && ($_SESSION[SESSION_PREFIX.'back_array'][$privilege_display_array[$i]] == "Y")) {
		$checked = "checked";
	}
	if ($i % 3 == 0) {
		echo '
				<tr>';
	}
	echo '
					<td width="33%">
						<input type="checkbox" class="checkbox" name="'.$privilege_display_array[$i].'" value="Y" '.$checked.'>
						'.$STRING[$privilege_display_array[$i]].'
					</td>';
	if ($i % 3 == 2) {
		echo '</tr>';
	}
}

if ($i % 3 != 0) {
	for ($j = 0; $j < (3 - ($i % 3)); $j++) {
		echo '
					<td width="33%">&nbsp;</td>';
	}
	echo '
				</tr>';
}
?>

				</table>
			</fieldset>

			<fieldset>
				<legend><?php echo $STRING['status_allow']?></legend>
				<table class="table-input-form" align="center">
<?php

$statusarray = GetStatusArray();

for ($i=0; $i < sizeof($statusarray); $i++) {
	$status_id= $statusarray[$i]->getstatusid();
	$status_name= $statusarray[$i]->getstatusname();
	if ($status_name == "") {
		continue;
	}
	$checked = "";
	if (isset($_SESSION[SESSION_PREFIX.'back_array'])) {
		if (isset($_SESSION[SESSION_PREFIX.'back_array']['C'.$i]) && ($_SESSION[SESSION_PREFIX.'back_array']['C'.$i] = $status_id)) {
			$checked = "checked";
			
		}
	} else {
		$checked = "checked";
	}

	if ($i % 3 == 0) {
		echo '
				<tr>';
	}
	
	echo '
					<td width="33%">
						<input type="checkbox" name="C'.$i.'" value="'.$status_id.'" class="checkbox" '.$checked.'>
						'.$status_name.'
					</td>';
	if ($i % 3 == 2) {
		echo '</tr>';
	}
}

if ($i % 3 != 0) {
	for ($j = 0; $j < (3 - ($i % 3)); $j++) {
		echo '
					<td width="33%">&nbsp;</td>';
	}
	echo '
				</tr>';
}

?>

				</table>
			</fieldset>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" class="button" name="B1">
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
include("../include/tail.php");
?>
