<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_new.php,v 1.18 2013/07/07 21:27:10 alex Exp $
 *
 */
include("../include/header.php");
include("../include/tinymce.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_document'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
?>
	
<script language="JavaScript" type="text/javascript">
<!--
var apply_count = 0;
function check_field(form)
{
	if (!form.subject.value.trim()) {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_document'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['subject'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	return true;
}

function MoveItem(FromList, ToList)
{
	var NewItem;
	var i,j;

	for (i=j=0; i<FromList.length; i++) {
		if (FromList.options[i].selected) {
			NewItem = new Option();
			NewItem.text = FromList.options[i].text;
			NewItem.value = FromList.options[i].value;
			ToList.length++;
			ToList.options[ToList.length-1] = NewItem;
		} else {
			NewItem = new Option();
			NewItem.text = FromList.options[i].text;
			NewItem.value = FromList.options[i].value;
			FromList.options[j] = NewItem;
			j++;
		}
	}
	FromList.length = j;

}

function AddItem()
{
	MoveItem(document.form1.all_class_name, document.form1.belong_class_name);
}

function RemoveItem()
{
	MoveItem(document.form1.belong_class_name, document.form1.all_class_name);
}

function OnLocalSubmit(form)
{
	var select='';
	var BelongClassList = document.form1.belong_class_name;

	if (apply_count > 0) {
		return false;
	}
	if (check_field(form) == false) {
		return false;
	}
	for (var i=0; i<BelongClassList.length; i++) {
		select = select + BelongClassList.options[i].value + ',';
	}
	document.form1.belong_class.value = select;
	apply_count++;
	return OnSubmit(form);
}

-->
</script>

<?php
TinyMCEScriptPrint("description");
?>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="document.php"><?php echo $STRING['title_document']?></a> /
	<?php echo $STRING['new_document']?>
</div>
<div id="main_container">
		<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
			<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_document.png" width="48" height="48" align="middle" border="0">
			<a href="document.php">
				<tt class="outline"><?php echo $STRING['title_document']?></tt>
			</a></td>
			<td nowrap valign="bottom">
			<a href="document.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
		</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<form method="POST" action="document_donew.php" onSubmit="return OnLocalSubmit(this);" name="form1" ENCTYPE="multipart/form-data">
			<input type="hidden" name="belong_class">

			<div class="item_prompt_large"><?php echo $STRING['subject'].$STRING['colon']?></div>
			<input class="input-form-text-field" type="text" name="subject" size="80" maxlength="300">
			
			<div class="item_prompt_large"><?php echo $STRING['description'].$STRING['colon']?></div>
			<textarea rows="25" name="description" style="width:95%" cols="80"><?php echo $_SESSION[SESSION_PREFIX.'back_array']['description']?></textarea>

			<div class="item_prompt_large"><?php echo $STRING['file_upload'].$STRING['colon']?></div>
			<input class="input-form-text-field" type="file" name="file" size="48" class="button">
			<?php echo PrintTip($STRING['hint_title'], $STRING['document_upload_hint'].$STRING['colon'].ini_get("upload_max_filesize"))?>

			<div class="item_prompt_large"><?php echo $STRING['document_setting'].$STRING['colon']?></div>

			<table width="90%" border="0">
			<tr>
				<td width="20%" valign="top"><?php echo $STRING['group_class'].$STRING['colon']?></td>
				<td width="80%">
					<select size="1" name="group_class">
						<option value="-1"><?php echo $STRING['all_groups']?></option>
<?php
$group_array = GetAllGroups();
for ($i = 0; $i < sizeof($group_array); $i++) {
	$group_id = $group_array[$i]->getgroupid();
	$group_name = htmlspecialchars($group_array[$i]->getgroupname());
	echo '
						<option value="'.$group_id.'">'.$group_name.'</option>';
}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo $STRING['allow_other_group'].$STRING['colon']?></td>
				<td>
					<input type="radio" name="allow_other_group" value="Y" checked><?php echo $STRING['yes']?>&nbsp;
					<input type="radio" name="allow_other_group" value="N"><?php echo $STRING['no']?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo $STRING['document_class'].$STRING['colon']?></td>
				<td>
					<table border="0" width="100%">
					<tr>
						<td width="40%" align="center" nowrap>
							<?php echo $STRING['all_document_class']?><br>
							<select name="all_class_name" size="10" multiple>

<?php
$get_class_sql = "select * from ".$GLOBALS['BR_document_class_table']." order by class_name";
$get_class_result = $GLOBALS['connection']->Execute($get_class_sql) or DBError(__FILE__.":".__LINE__);

$all_class_id = array();
$all_class_name = array();
while ($row = $get_class_result->FetchRow()) {
	$document_class_id = $row["document_class_id"];
	$class_name = $row["class_name"];
	array_push($all_class_id, $document_class_id);
	array_push($all_class_name, $class_name);
}

// List all category
for ($i = 0; $i < sizeof($all_class_id); $i++) {
	$document_class_id = $all_class_id[$i];
	$class_name = $all_class_name[$i];
	echo '
								<option value="'.$document_class_id.'">'.$class_name.'</option>';
}
?>
							</select>
						</td>
						<td width="20%" nowrap align="center">
							<p><button name="add" style="width: 120px;" type="button" onClick="AddItem();">
							<?php echo $STRING['access_add']?></button></p>
							<p><button name="remove" style="width: 120px;" type="button" onClick="RemoveItem();">
							<?php echo $STRING['access_remove']?></button></p>
						</td>
						<td width="40%" nowrap align="center">
							<?php echo $STRING['belong_document_class']?><br>
							<select size="10" name="belong_class_name" multiple>
							</select>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			</table>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" name="B1" class="button"></p>
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php

include("../include/tail.php");
?>
