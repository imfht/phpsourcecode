<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: schedule_new.php,v 1.11 2010/07/26 09:05:26 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/tinymce.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_edit_schedule'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

TinyMCEScriptPrint("description");
?>
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
	<?php echo $STRING['new_schedule']?>
</div>

<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_schedule.png" width="48" height="48" align="middle" border="0">
				<a href="schedule.php"><tt class="outline"><?php echo $STRING['title_schedule']?></tt></a>
			</td>
			<td nowrap valign="bottom">
				<a href="schedule.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="schedule_donew.php" onsubmit="return check1();" name="form1">
			<table border="1" align="center" cellpadding="2" cellspacing="0" style="border-collapse: collapse" width="700">
			<tr>
				<td width="100%" class="title" colspan="2" align="center"><?php echo $STRING['new_schedule']?>
				</td>
			</tr>
			<tr>
				<td width="170" class="prompt">
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['subject'].$STRING['colon']?>
				</td>
				<td width="530" class="content">
						<input type="text" class="input-form-text-field" name="subject" size="50" maxlength="100" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']["subject"]?>">
				</td>
			</tr>
			<tr>
				<td class="prompt">
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['date'].$STRING['colon']?>
				</td>
				<td class="content">
<?php
//list($year, $month, $day) = explode('-', $_GET['date']);
//$this_time = mktime(0, 0, 0, $month, $day, $year);
$this_time = $_GET['time'];
$show_date = date(GetDateFormat(), $this_time);
echo $show_date;
echo '<input type="hidden" name="time" value="'.$_GET['time'].'">';
?>

				</td>
			</tr>
			<tr>
				<td class="prompt prompt_align_top">
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
	$project_option .= "<option value=\"$visible_project_id\">$visiable_project_name</option>";
}

if ($project_option != "") {
	echo '<input type="radio" name="schedule_type" value="project" checked> ';
	echo $STRING['project_schedule'].$STRING['colon'];
	echo '<select name="project_id" size="1">';
	echo $project_option;
	echo '</select><br>';
	echo '<input type="radio" name="schedule_type" value="personal"> '.$STRING['personal_schedule'].'<br>';
} else {
	echo '<input type="radio" name="schedule_type" value="project" disabled>';
	echo $STRING['project_schedule'].'<br>';
	echo '<input type="radio" name="schedule_type" value="personal" checked> '.$STRING['personal_schedule'].'<br>';
}
?>

				</td>
			</tr>
			<tr>
				<td class="prompt">
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['publish_schedule'].$STRING['colon']?>
					<?php echo PrintTip($STRING['hint_title'], $STRING['publish_hint'])?>
				</td>
				<td class="content">
					<input type="radio" name="publish" value="Y" checked><?php echo $STRING['yes']?>&nbsp;
					<input type="radio" name="publish" value="N"><?php echo $STRING['no']?>
				</td>
			</tr>
			<tr>
				<td class="prompt">
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['schedule_emailto'].$STRING['colon']?>
					<?php echo PrintTip($STRING['hint_title'], $STRING['schedule_emailto_hint'])?>
				</td>
				<td class="content">
					<input type="text" class="input-form-text-field" name="email_to" size="50" maxlength="250">
				</td>
			</tr>
			<tr>
				<td class="prompt prompt_align_top">
					<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
					<?php echo $STRING['description'].$STRING['colon']?>
					</td>
				<td class="content">
					<textarea rows="20" class="input-form-text-textarea" name="description" style="width:99%" cols="50"><?php echo $_SESSION[SESSION_PREFIX.'back_array']['content']?></textarea>
				</td>
			</tr>
		</table>
		<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" name="B1" class="button"></p>
		</form>
	</div>
</div>

<?php
include("../include/tail.php");
?>
