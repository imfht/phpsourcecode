<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_report_update.php,v 1.13 2013/07/07 21:25:52 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/project_function.php");
include("../include/customer_function.php");
include("../include/feedback_report_function.php");
include("../include/tinymce.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_feedback'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (!isset($_GET['report_id']) || ($_GET['report_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];

$extra_params = GetExtraParams($_GET, "search_key,customer_filter,page,sort_by,sort_method");

TinyMCEScriptPrint("description");

?>

<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.form1;
	var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
	if(!f.summary.value) {
		y+='-<?php echo addslashes($STRING['summary'])?><br>';
	}
	if(!f.version.value) {
		y+='-<?php echo addslashes($STRING['version'])?><br>';
	}
	if(y=='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>') {
		return OnSubmit(f);
	} else {
		ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['edit_report'])?>',
		msg: y,
		buttons: ['ok'],
		width: 300
		});
		return false;
	}
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="feedback_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><?php echo htmlspecialchars($project_name)?>
	<?php echo $STRING['title_feedback']?></a> /<?php echo $STRING['edit_report']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="feedback_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>">
					<tt class="outline"><?php echo htmlspecialchars($project_name)?></tt>
				</a>
			</td>
<?php
$get_map_sql = "select * from proj".$_GET['project_id']."_feedback_map_table where feedback_report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
$map_result = $GLOBALS['connection']->Execute($get_map_sql) or DBError(__FILE__.":".__LINE__);
$line = $map_result->Recordcount();
		
if ($line == 0) {
	echo '
			<td nowrap valign="bottom">
				<a href="feedback_report_import.php?project_id='.$_GET['project_id'].'&report_id='.$_GET['report_id'].'&from='.$_SERVER['PHP_SELF'].$extra_params.'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/import.png" border="0" align="middle">
					'.$STRING['import'].'</a>
			</td>';
}

?>
				
			<td nowrap valign="bottom">
				<a href="feedback_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>">
					<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle">
					<?php echo $STRING['back']?>
				</a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="feedback_report_doupdate.php" onsubmit="return check1();" name="form1" ENCTYPE="multipart/form-data">
			<input type="hidden" name="report_id" value="<?php echo $_GET['report_id']?>">

<?php
$output = GetFeedbackReportOutput($_GET['project_id'], $_GET['report_id'], "update");

echo $output;
?>

			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
		</form>
	</div>
</div>
<?php
PrintGotoTop();
include("../include/tail.php");
?>
