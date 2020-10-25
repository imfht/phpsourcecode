<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_export.php,v 1.14 2013/10/12 14:59:00 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];

$user_sql = "select * from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$user_result = $GLOBALS['connection']->Execute($user_sql) or DBError(__FILE__.":".__LINE__);
$line = $user_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "user");
}
$setting_row = $user_result->FetchRow();
?>
<form method="GET" name="back_form" action="project_list.php">
<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
<input type="hidden" name="search_key" value="<?php echo $_GET['search_key']?>">
<input type="hidden" name="search_type" value="<?php echo $_GET['search_type']?>">
<input type="hidden" name="page" value="<?php echo $_GET['page']?>">
<input type="hidden" name="choice_filter" value="<?php echo $_GET['choice_filter']?>">
<input type="hidden" name="sort_by" value="<?php echo $_GET['sort_by']?>">
<input type="hidden" name="sort_method" value="<?php echo $_GET['sort_method']?>">
<input type="hidden" name="label" value="<?php echo $_GET['label']?>">
<?php
for ($i=0; $i<sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] == 't') {
		echo '<input type="hidden" name="'.$show_column_array[$i].'" value="'.$_GET[$show_column_array[$i]].'">';
		echo "\n";
	}
}
?>
</form>

<script language="JavaScript" type="text/javascript">
<!--
function DocumentChange(form)
{
	if (form.search_key.value == '') {
		form.search_key.disabled = true;
	} else {
		form.search_key.disabled = false;
	}
	if (form.choice_filter.value == '') {
		form.choice_filter.disabled = true;
	} else {
		form.choice_filter.disabled = false
	}
	if (form.sort_by.value == '') {
		form.sort_by.disabled = true;
	} else {
		form.sort_by.disabled = false;
	}
	if (form.sort_method.value == '') {
		form.sort_method.disabled = true;
	} else {
		form.sort_method.disabled = false;
	}
	if (form.label.value == '') {
		form.label.disabled = true;
	} else {
		form.label.disabled = false;
	}
	if ((form.page.value == '') || (form.page.value == -1)) {
		form.page.disabled = true;
	} else {
		form.page.disabled = false;
	}
<?php
for ($i=0; $i<sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] != 't') {
		continue;
	}
	echo '
	if (form.'.$show_column_array[$i].' && (form.'.$show_column_array[$i].'.value == -1 || form.'.$show_column_array[$i].'.value == \'\')) {
		form.'.$show_column_array[$i].'.disabled = true;
	} else {
		form.'.$show_column_array[$i].'.disabled = false;
	}
';
}
?>
	form.submit();
}

function CheckIconv(form)
{
<?php
if (function_exists(iconv)) {
	echo '	return DocumentChange(form);';
} else {
	echo '
	alert(\'This function requires iconv extension but your PHP does not support.\');
	return false;
	';
}
?>

}

</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="JavaScript:DocumentChange(document.back_form);" style="cursor: pointer;"><?php echo htmlspecialchars($project_name)?></a> /
	<?php echo $STRING['export']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_export.png" width="48" height="48" align="middle" border="0">
					<tt class="outline"><?php echo $STRING['export']?></tt>
			</td>
			<td nowrap valign="bottom">
				<a href="JavaScript:DocumentChange(document.back_form);" style="cursor: pointer;"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>

			<form method="POST" name="main_form" action="project_doexport.php">
				<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
				<input type="hidden" name="search_key" value="<?php echo stripslashes($_GET['search_key'])?>">
				<input type="hidden" name="search_type" value="<?php echo $_GET['search_type']?>">
				<input type="hidden" name="page" value="<?php echo stripslashes($_GET['page'])?>">
				<input type="hidden" name="choice_filter" value="<?php echo stripslashes($_GET['choice_filter'])?>">
				<input type="hidden" name="sort_by" value="<?php echo stripslashes($_GET['sort_by'])?>">
				<input type="hidden" name="sort_method" value="<?php echo stripslashes($_GET['sort_method'])?>">
				<input type="hidden" name="label" value="<?php echo stripslashes($_GET['label'])?>">
<?php
for ($i=0; $i<sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] == 't') {
		echo '
				<input type="hidden" name="'.$show_column_array[$i].'" value="'.$_GET[$show_column_array[$i]].'">';
	}
}
?>

			<fieldset>
				<legend><?php echo $STRING['export_columns']?></legend>
				<table class="table-main-list" align="center">
				<tr>
					<td width="25%">
						<input type="checkbox" name="show_id" class="checkbox" value="Y" checked><?php echo $STRING['id']?>
					</td>
					<td width="25%">
						<input type="checkbox" name="show_summary" class="checkbox" value="Y" checked><?php echo $STRING['summary']?>
					</td>
<?php
$shift = 2; // Already has 2 item above.
for ($i = $shift; $i<sizeof($show_column_array)+$shift; $i++) {
	if ($i % 4 == 0) {
		echo '
				<tr>';
	}
	$show_column = "show_".$show_column_array[$i-$shift];
	if ($setting_row[$show_column]=='t') {
		$checked="checked";
	}else {
		$checked="";
	}
	echo '
					<td width="25%">
						<input type="checkbox" name="'.$show_column.'" class="checkbox" value="Y" '.$checked.'>'.$STRING[$show_column_array[$i-$shift]].'
					</td>';

	if ($i % 4 == 3) {
		echo '
				</tr>';
	}
}

if ($i % 4 != 0) {
	for ($j = 0; $j < (4 - ($i % 4)); $j++) {
		echo '
					<td>&nbsp;</td>';
	}
	echo '
				</tr>';
}

?>

				</table>
			</fieldset>
				<p align="center"><input type="button" value="<?php echo $STRING['export']?>" name="B1" class="button" onClick="CheckIconv(document.main_form);"></p>
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
include("../include/tail.php");
?>
