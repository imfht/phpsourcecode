<?php 
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_edit.php,v 1.15 2013/06/29 20:18:15 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/tinymce.php");

AuthCheckAndLogin();
	
if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_faq'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['faq_id'])) {
	$_GET['faq_id'] = $_SESSION[SESSION_PREFIX.'back_array']['faq_id'];
}

if ($_GET['faq_id'] == "") {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "faq");
}

// Get FAQ Content
$sql = "select * from ".$GLOBALS['BR_faq_content_table']."  where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "faq");
}

$project_id = $result->fields["project_id"];
$question = $result->fields["question"];
$answer = $result->fields["answer"];
$created_by = $result->fields["created_by"];
$created_date = $result->fields["created_date"];
$is_verified = $result->fields["is_verified"];
$assign_to = $result->fields["assign_to"];

// Get project data
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$project_line = $project_result->Recordcount();
if ($project_line == 1) {
	$project_name = $project_result->fields["project_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$extra_params = GetExtraParams($_GET, "search_key,faq_class,page");
?>
<script language="JavaScript" type="text/javascript">
<!--
var apply_count = 0;

function check_field()
{
	var f=document.form1;
	if(!f.question.value.trim()){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_faq'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['question'], $STRING['no_empty']));?>',
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
	if (check_field() == false) {
		return false;
	}
	for (var i=0; i<BelongClassList.length; i++) {
		select = select + BelongClassList.options[i].value + ',';
	}
	document.form1.belong_class.value = select;
	apply_count++;
	return OnSubmit(form);
}

function CheckAssign()
{
	var verified = document.form1.is_verified[0].checked;

	if (verified) {
		document.form1.assign_to.disabled = true;
	} else {
		document.form1.assign_to.disabled = false;
	}
}

-->
</script>
<?php 
TinyMCEScriptPrint("answer");
?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b>
	/ <a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="faq_admin.php?project_id=<?php echo $project_id.$extra_params?>"><?php echo $project_name?>
	<?php echo $STRING['faq']?></a> / <?php echo $STRING['edit_faq']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_faq.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['edit_faq']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom"></td>
			<td nowrap valign="bottom">
<?php 
	if ($_GET['from_show'] == 'y') {
		echo '<a href="faq_show.php?faq_id='.$_GET['faq_id'].$extra_params.'">';
	} else {
		echo '<a href="faq_admin.php?project_id='.$project_id.$extra_params.'">';
	}
				
?>
					<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<form method="POST" action="faq_doedit.php" onSubmit="return OnLocalSubmit(this);" name="form1">
			<input type="hidden" name="belong_class">
			<input type="hidden" name="faq_id" value="<?php echo $_GET['faq_id']?>">

			<div class="item_prompt_large"><?php echo $STRING['question'].$STRING['colon']?></div>
			<textarea class="input-form-text-textarea" rows="4" name="question" style="width:95%" cols="80"><?php echo $question?></textarea>

			<div class="item_prompt_large"><?php echo $STRING['answer'].$STRING['colon']?></div>
			<textarea name="answer" style="width:95%" rows="25" cols="80"><?php echo $answer?></textarea>

			<div class="item_prompt_large"><?php echo $STRING['faq_setting'].$STRING['colon']?></div>
			<table width="90%" border="0">
			<tr>
				<td width="20%" valign="top"><?php echo $STRING['assign_to'].$STRING['colon']?>
					<?php  echo PrintTip($STRING['hint_title'], $STRING['faq_verified_hint']);?>
				</td>
				<td width="80%">
					<select size="1" name="assign_to">
						<option value="-1" SELECTED> </option>
<?php 
$userarray = GetAllUsers(0, 0);
for ($i=0;$i<sizeof($userarray);$i++) {
	if (($assign_to == $userarray[$i]->getuserid()) ) {
		echo '
						<option value="'.$userarray[$i]->getuserid().'" selected>'.$userarray[$i]->getusername().'</option>';
	} else {
		echo '
						<option value="'.$userarray[$i]->getuserid().'">'.$userarray[$i]->getusername().'</option>';
	}
}
?>
        
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo $STRING['is_verified'].$STRING['question_mark']?></td>
				<td>
<?php 
	if ($is_verified == 't') {
		echo '
					<input type="radio" value="t" name="is_verified" onClick="return CheckAssign();" checked>'.$STRING["yes"].'&nbsp;&nbsp
					<input type="radio" value="f" name="is_verified" onClick="return CheckAssign();">'.$STRING["no"];
	} else {
		echo '
					<input type="radio" value="t" name="is_verified" onClick="return CheckAssign();">'.$STRING["yes"].'&nbsp;&nbsp
					<input type="radio" value="f" name="is_verified" onClick="return CheckAssign();" checked>'.$STRING["no"];
	}
?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo $STRING['faq_class'].$STRING['colon']?></td>
				<td>
					<table border="0" width="100%">
					<tr>
						<td width="40%" align="center" nowrap>
							<?php echo $STRING['all_class_name']?><br>
							<select name="all_class_name" size="10" multiple>

<?php 
$old_class_sql = "select * from ".$GLOBALS['BR_faq_map_table']." where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id']);
$old_class_result = $GLOBALS['connection']->Execute($old_class_sql) or DBError(__FILE__.":".__LINE__);
$old_class_id = array();
while ($row = $old_class_result->FetchRow()) {
	$faq_class_id = $row["faq_class_id"];
	array_push($old_class_id, $faq_class_id);
}

$get_class_sql = "select * from ".$GLOBALS['BR_faq_class_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
$get_class_result = $GLOBALS['connection']->Execute($get_class_sql) or DBError(__FILE__.":".__LINE__);

$all_class_id = array();
$all_class_name = array();
while ($row = $get_class_result->FetchRow()) {
	$faq_class_id = $row["faq_class_id"];
	$class_name = $row["class_name"];
	array_push($all_class_id, $faq_class_id);
	array_push($all_class_name, $class_name);
}

// List all FAQ category
for ($i = 0; $i < sizeof($all_class_id); $i++) {
	$faq_class_id = $all_class_id[$i];
	$class_name = $all_class_name[$i];
	if (IsInArray($old_class_id, $faq_class_id) >= 0) {
		continue;
	}
	echo '
								<option value="'.$faq_class_id.'">'.$class_name.'</option>';
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
							<?php echo $STRING['belong_class_name']?><br>
							<select size="10" name="belong_class_name" multiple>
<?php 
// List old FAQ category
for ($i = 0; $i < sizeof($old_class_id); $i++) {
	$faq_class_id = $old_class_id[$i];

	$class_name = "";
	for ($j = 0; $j < sizeof($all_class_id); $j++) {
		if ($all_class_id[$j] == $faq_class_id) {
			$class_name = $all_class_name[$j];
			break;
		}
	}
	if ($class_name == "") {
		continue;
	}

	echo '
								<option value="'.$faq_class_id.'">'.$class_name.'</option>';
}
?>

							</select>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button">
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php 
include("../include/tail.php");
?>
