<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: schedule_edit.php,v 1.13 2013/07/05 20:17:48 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/tinymce.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_edit_schedule'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['schedule_id']) && isset($_SESSION[SESSION_PREFIX.'back_array']['schedule_id'])) {
	$_GET['schedule_id'] = $_SESSION[SESSION_PREFIX.'back_array']['schedule_id'];
}

if (!isset($_GET['schedule_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "id");
}

$sql = "select ".$GLOBALS['BR_schedule_table'].".*, ".$GLOBALS['BR_user_table'].".username
		from ".$GLOBALS['BR_schedule_table'].", ".$GLOBALS['BR_user_table']." 
	  	where schedule_id=".$GLOBALS['connection']->QMagic($_GET['schedule_id'])." and 
		".$GLOBALS['BR_schedule_table'].".created_by=".$GLOBALS['BR_user_table'].".user_id";

$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "schedule", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "title_schedule");
}

$date = $result->UserTimeStamp($result->fields["date"], "Y-m-d");
$created_by = $result->fields["username"];
$create_uid = $result->fields["created_by"];
if ($_SESSION[SESSION_PREFIX.'uid'] != 0 && ($create_uid != $_SESSION[SESSION_PREFIX.'uid'])) {
	WriteSyslog("error", "syslog_not_found", "schedule", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "title_schedule");
}
$subject = $result->fields["subject"];
$description = $result->fields["description"];
$project_id = $result->fields["project_id"];
$publish = $result->fields["publish"];
$email_to = $result->fields["email_to"];

TinyMCEScriptPrint("description");
?>
<p>&nbsp;</p>
<script language="JavaScript">
<!--
function check1()
{
	var f = document.form1;

	if(!f.subject.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_schedule'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['subject'], $STRING['no_empty']));?>',
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
	<a href="schedule.php"><?php echo $STRING['title_schedule']?></a> /
	<?php echo $STRING['edit_schedule']?>
</div>

<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_schedule.png" width="48" height="48" align="middle" border="0">
				<a href="schedule.php">
					<tt class="outline"><?php echo $STRING['title_schedule']?></tt>
				</a>
			</td>
			<td nowrap valign="bottom">
				<a href="schedule.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="schedule_doedit.php" onsubmit="return check1();" name="form1">
		<input type="hidden" name="schedule_id" value="<?php echo $_GET['schedule_id']?>">
			<table border="1" align="center" cellpadding="2" cellspacing="0" style="border-collapse: collapse" width="700">
			<tr>
				<td width="100%" class="title" colspan="2" align="center"><?php echo $STRING['edit_schedule']?>
				</td>
			</tr>
			<tr>
				<td width="170" class="prompt" nowrap>
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['subject'].$STRING['colon']?>
				</td>
				<td width="530" class="content">
					<input type="text" class="input-form-text-field" name="subject" size="50" maxlength="100" value="<?php echo $subject?>">
				</td>
			</tr>
			<tr>
				<td class="prompt" nowrap>
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['date'].$STRING['colon']?>
				</td>
				<td class="content">
<?php
list($year, $month, $day) = explode('-', $date);
echo '<select name="year" size="1">';
for ($i = 2005; $i < date("Y")+5; $i++) {
	if ($year == $i) {
		echo '<option value="'.$i.'" selected>'.$i.'</option>';
	} else {
		echo '<option value="'.$i.'">'.$i.'</option>';
	}
}
echo '</select>';
echo '<select name="month" size="1">';
for ($i = 1; $i <= 12; $i++) {
	if ($month == $i) {
		echo '<option value="'.$i.'" selected>'.$i.'</option>';
	} else {
		echo '<option value="'.$i.'">'.$i.'</option>';
	}
}
echo '</select>';
echo '<select name="day" size="1">';
for ($i = 1; $i <= 31; $i++) {
	if ($day == $i) {
		echo '<option value="'.$i.'" selected>'.$i.'</option>';
	} else {
		echo '<option value="'.$i.'">'.$i.'</option>';
	}
}
echo '</select>';

?>

				</td>
			</tr>
			<tr>
				<td class="prompt" nowrap>
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['created_by'].$STRING['colon']?>
				</td>
				<td class="content">
					<?php echo $created_by?>
				</td>
			</tr>
			<tr>
				<td class="prompt">
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['schedule_type'].$STRING['colon']?>
				</td>
				<td class="content">

<?php
$project_array = GetAllProjects($_SESSION[SESSION_PREFIX.'uid']);
$project_option = "";
for ($i = 0; $i < sizeof($project_array); $i++) {
	$visible_project_id = $project_array[$i]->getprojectid();
	$visiable_project_name = $project_array[$i]->getprojectname();
	if ($project_id == $visible_project_id) {
		$project_option .= "<option value=\"$visible_project_id\" selected>$visiable_project_name</option>";
	} else {
		$project_option .= "<option value=\"$visible_project_id\">$visiable_project_name</option>";
	}
	
}

if ($project_option != "") {
	if ($project_id > 0) {
		echo '<input type="radio" name="schedule_type" value="project" checked> ';
	} else {
		echo '<input type="radio" name="schedule_type" value="project"> ';
	}
	echo $STRING['project_schedule'].$STRING['colon'];
	echo '<select name="project_id" size="1">';
	echo $project_option;
	echo '</select><br>';
	if ($project_id > 0) {
		echo '<input type="radio" name="schedule_type" value="personal"> '.$STRING['personal_schedule'].'<br>';
	} else {
		echo '<input type="radio" name="schedule_type" value="personal" checked> '.$STRING['personal_schedule'].'<br>';
	}
} else {
	echo '<input type="radio" name="schedule_type" value="project" disabled>';
	echo $STRING['project_schedule'].'<br>';
	echo '<input type="radio" name="schedule_type" value="personal" checked> '.$STRING['personal_schedule'].'<br>';
}
?>

				</td>
			</tr>
			<tr>
				<td class="prompt" nowrap>
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['publish_schedule'].$STRING['colon']?>
					<?php echo PrintTip($STRING['hint_title'], $STRING['publish_hint'])?>
				</td>
				<td class="content">
<?php
if ($publish == 't') {
	echo '<input type="radio" name="publish" value="Y" checked>'.$STRING['yes'].'&nbsp;';
	echo '<input type="radio" name="publish" value="N">'.$STRING['no'].'&nbsp;';
} else {
	echo '<input type="radio" name="publish" value="Y">'.$STRING['yes'].'&nbsp;';
	echo '<input type="radio" name="publish" value="N" checked>'.$STRING['no'].'&nbsp;';
}
?>       
				</td>
			</tr>
			<tr>
				<td class="prompt" nowrap>
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['schedule_emailto'].$STRING['colon']?>
					<?php echo PrintTip($STRING['hint_title'], $STRING['schedule_emailto_hint'])?>
				</td>
				<td class="content">
					<input type="text" class="input-form-text-field" name="email_to" value="<?php echo $email_to?>" size="50" maxlength="250">
				</td>
			</tr>
			<tr>
				<td class="prompt" nowrap>
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['description'].$STRING['colon']?>
				</td>
				<td class="content">
					<textarea rows="20" class="input-form-text-textarea" name="description" style="width:99%" cols="50"><?php echo $description?></textarea>
				</td>
			</tr>
			</table>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
		</form>
	</div>
</div>      

<?php
include("../include/tail.php");
?>
