<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_update.php,v 1.11 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/project_function.php");
include("include/report_function.php");
include("include/tinymce.php");

AuthCheckAndLogin();

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (!isset($_GET['report_id']) || ($_GET['report_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line == 1) {                  
	$project_name = $project_result->fields["project_name"];
} else {
	ErrorPrintOut("no_such_xxx", "project");
}

$report_sql = "select created_by from proj".$_GET['project_id']."_feedback_table 
			where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id'])." and 
			customer_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_customer']);
$report_result = $GLOBALS['connection']->Execute($report_sql) or DBError(__FILE__.":".__LINE__);
$line = $report_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}
$created_by = $report_result->fields["created_by"];

if (($_SESSION[SESSION_PREFIX.'feedback_customer'] == 0) &&
	($_SESSION[SESSION_PREFIX.'feedback_email'] != $created_by)) {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}

$extra_params = GetExtraParams($_GET, "search_key,page,sort_by,sort_method");

$output = GetReportOutput($_GET['project_id'], $_GET['report_id'], "update");
if ($output == "") {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}

TinyMCEScriptPrint("description");

?>

<script language="JavaScript">
<!--
function check1()
{
    var f=document.form1;
    var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
		
	if (!f.summary.value) {
		y += '    -<?php echo addslashes($STRING['summary'])?><br>';
	}
	if (!f.version.value) {
		y += '    -<?php echo addslashes($STRING['version'])?><br>';
	}
	if (y == '<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>') {
		return OnSubmit(f);
	} else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_report'])?>',
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
	<a href="index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><?php echo htmlspecialchars($project_name)?></a> /
	<?php echo $STRING['edit_report']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>">
					<tt class="outline"><?php echo htmlspecialchars($project_name)?></tt>
				</a>
			</td>
			<td nowrap valign="bottom">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>">
					<img src="images/return.png" border="0" align="middle">
					<?php echo $STRING['back']?>
				</a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="report_doupdate.php" onsubmit="return check1();" name="form1" ENCTYPE="multipart/form-data">
			<input type="hidden" name="report_id" value="<?php echo $_GET['report_id']?>">

<?php
echo $output;
?>
		<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
		</form>
	</div>
</div>

<?php
PrintGotoTop();

include("include/tail.php");
?>
