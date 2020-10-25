<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_setting.php,v 1.13 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!isset($_GET['user_id'])) {
	$_GET['user_id'] = $_SESSION[SESSION_PREFIX.'uid'];
}

if ($_SESSION[SESSION_PREFIX.'uid'] != $_GET['user_id']) {
	if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_privilege");
	}
} 

$user_sql = "select * from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$user_result = $GLOBALS['connection']->Execute($user_sql) or DBError(__FILE__.":".__LINE__);
$line = $user_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "user");
}
$username = $user_result->fields["username"];
$language = $user_result->fields["language"];

$setting_row = $user_result->FetchRow();

?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['preference']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_preference.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['preference']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="../system/system.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3><?php echo $username?></h3>
			<form method="POST" action="user_dosetting.php" OnSubmit="return OnSubmit(this);">
			<input type="hidden" name="user_id" value="<?php echo $_GET['user_id']?>">
			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>

				<table class="table-input-form">
				<tr>
					<td class="item_prompt_small" width="250">
						<?php echo $STRING['language'].$STRING['colon']?>
					</td>
					<td width="250">
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
	  
						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['report_per_page'].$STRING['colon']?>
					</td>
					<td>
						<input type="text" class="input-form-text-field" name="perpage" size="20" value="<?php echo $setting_row["perpage"]?>" maxlength="5">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['default_filter'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="default_filter">
<?php
if ($setting_row["default_filter"] == 0) {
	echo '
							<option value="0" selected>'.$STRING['none'].'</option>';
} else {
	echo '
							<option value="0">'.$STRING['none'].'</option>';
}

// system default filters
$filter_sql = "select * from ".$GLOBALS['BR_filter_table']." where filter_id < 0";
$filter_result = $GLOBALS['connection']->Execute($filter_sql) or DBError(__FILE__.":".__LINE__);
$line = $filter_result->Recordcount();
if ($line != 0) {
	while ($row = $filter_result->FetchRow()){
		$filter_id = $row["filter_id"];
		if ($filter_id == -1) {
			$filter_name = $STRING['assigned_to_me'];
		} else if ($filter_id == -2) {
			$filter_name = $STRING['fixed_by_me_last_week'];
		} else {
			$filter_name = $row["filter_name"];
		}
		
		if (($filter_id==$setting_row["default_filter"])&&($filter_id!=0)) {
			echo '
							<option value="'.$filter_id.'" selected>'.$filter_name.'</option>';
			$is_default="";
		}else{
			echo '
							<option value="'.$filter_id.'">'.$filter_name.'</option>';
		}
	}
}

// �q userfilter �� table ���A��X�ϥΪ����� filter ���]�w������
$filter_sql = "select * from ".$GLOBALS['BR_filter_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$filter_result = $GLOBALS['connection']->Execute($filter_sql) or DBError(__FILE__.":".__LINE__);
$line = $filter_result->Recordcount();
// �p�G���ϥΪ� filter ����ơA�h��ܡC�_�h��� None
if ($line != 0) {
	while ($row = $filter_result->FetchRow()){
		$filter_id = $row["filter_id"];
		$filter_name = $row["filter_name"];
		if (($filter_id==$setting_row["default_filter"])&&($filter_id!=0)) {
			echo '
							<option value="'.$filter_id.'" selected>'.$filter_name.'</option>';
			$is_default="";
		}else{
			echo '
							<option value="'.$filter_id.'">'.$filter_name.'</option>';
		}
	}
}

?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['show_shared_filter'].$STRING['question_mark']?>
					</td>
					<td>
<?php
// ��ܬO�_�ϥΪ��ؽu
if ($setting_row["show_shared_filter"]== 'f') {
	echo '
						<input type="radio" value="Y" name="show_shared_filter" class="checkbox">'.$STRING['yes'].'&nbsp;
						<input type="radio" name="show_shared_filter" value="N" class="checkbox" checked>'.$STRING['no'];
} else {
	echo '
						<input type="radio" value="Y" name="show_shared_filter" class="checkbox" checked>'.$STRING['yes'].'&nbsp;
						<input type="radio" name="show_shared_filter" value="N" class="checkbox">'.$STRING['no'];
}
?>
    
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['show_in_blank'].$STRING['question_mark']?>
					</td>
					<td>
<?php
if ($setting_row["show_in_blank"]== 'f') {
	echo '
						<input type="radio" value="Y" name="show_in_blank" class="checkbox">'.$STRING['yes'].'&nbsp;
						<input type="radio" name="show_in_blank" value="N" class="checkbox" checked>'.$STRING['no'];
} else {
	echo '
						<input type="radio" value="Y" name="show_in_blank" class="checkbox" checked>'.$STRING['yes'].'&nbsp;
						<input type="radio" name="show_in_blank" value="N" class="checkbox">'.$STRING['no'];
}
?>
					</td>
				</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend><?php echo $STRING['columns_to_display']?></legend>

				<table class="table-input-form">
<?php

for ($i = 0; $i<sizeof($show_column_array); $i++) {

	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column]=='t') {
		$checked="checked";
	}else {
		$checked="";
	}
	if ($i % 2 == 0) {
		echo '
				<tr>';
	}
	echo '
					<td width="50%">
						<input type="checkbox" name="'.$show_column.'" class="checkbox" value="Y" '.$checked.'>
						'.$STRING[$show_column_array[$i]].'
					</td>';
	if ($i % 2 == 1) {
		echo '
				</tr>';
	}
}

if ($i % 2 != 0) {
	for ($j = 0; $j < (2 - ($i % 2)); $j++) {
		echo '
					<td width="50%">&nbsp;</td>';
	}
	echo '
				</tr>';
}

?>  

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

<?php
include("../include/tail.php");
?>
