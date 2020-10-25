<?php
/* Copyright c 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: label_edit.php,v 1.4 2013/06/30 21:45:28 alex Exp $
 *
 */

include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_label'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!$_GET['label_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "label_id");
}

$sql = "SELECT * FROM ".$GLOBALS['BR_label_table']." WHERE label_id=".$GLOBALS['connection']->QMagic($_GET['label_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);;
$line = $result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "label");
} 
$project_id = $result->fields["project_id"];
$label_name = $result->fields["label_name"];
$label_color = $result->fields["color"];

if (CheckProjectAccessable($project_id, $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$project_line = $project_result->Recordcount();
if ($project_line == 1) {
	$project_name = $project_result->fields["project_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
    var f=document.form1;
    if(!f.label_name.value) {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_label'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['label'], $STRING['no_empty']));?>',
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
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_list.php?project_id=<?php echo $project_id?>"><?php echo $project_name?></a> /
	<a href="label_admin.php?project_id=<?php echo $project_id?>"><?php echo $STRING['label_management']?></a> /
	<?php echo $STRING['edit_label']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_label.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['edit_label']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="label_admin.php?project_id=<?php echo $project_id?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<form method="POST" action="label_doedit.php" name="form1" onsubmit="return check1();">
			<input type="hidden" name="project_id" value="<?php echo $project_id?>">
			<input type="hidden" name="label_id" value="<?php echo $_GET['label_id']?>">
			<table class="table-input-form" align="center">
			<tr>
				<td class="item_prompt_small">
					<?php echo $STRING['label'].$STRING['colon']?>
					<input class="input-form-text-field" type="text" name="label_name" value="<?php echo $label_name?>" size="30" maxlength="30">
				</td>
			</tr>
			</table>

			<fieldset>
				<legend><?php echo $STRING['label_color']?></legend>
				<table class="table-input-form" align="center">
<?php
$i = 0;
foreach ($label_color_array as $color) {
	if ($i == $label_color) {
		$checked = "checked";
	} else {
		$checked = "";
	}
	if ($i % 6 == 0) {
		echo '
				<tr>';
	}
	echo '
					<td width="16%">
						<input type="radio" name="color" value="'.$i.'" '.$checked.'>
						<span style="color:'.$color[0].';background-color:'.$color[1].'">'.$STRING['label'].'</span>
					</td>';
	if ($i % 6 == 5) {
		echo '</tr>';
	}
	$i++;
}

if ($i % 6 != 0) {
	for ($j = 0; $j < (6 - ($i % 6)); $j++) {
		echo '
					<td width="33%">&nbsp;</td>';
	}
	echo '
				</tr>';
}
?>

				</table>
			</fieldset>

			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" class="button" name="B1">
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
include("../include/tail.php");
?>
