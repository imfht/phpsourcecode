<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_update.php,v 1.25 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/customer_function.php");
include("../include/project_function.php");
include("../include/status_function.php");
include("../include/user_function.php");
include("../include/report_function.php");

AuthCheckAndLogin();

include("../include/area_js.php");
include("../include/tinymce.php");

if (!($GLOBALS['Privilege'] & $GLOBALS['can_update_report'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (!$_GET['report_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$project_sql="select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];
$version_pattern = $project_result->fields["version_pattern"];

// ��o��l���
$get_report_sql = "select * from proj".$_GET['project_id']."_report_table 
	where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
$get_report_result = $GLOBALS['connection']->Execute($get_report_sql) or DBError(__FILE__.":".__LINE__);
$get_report_line = $get_report_result->Recordcount();
if ($get_report_line != 1) {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}

$fixed_in_version = $get_report_result->fields["fixed_in_version"];
$fixed_in_version = chop($fixed_in_version);

$output = GetReportOutput($_GET['project_id'], $_GET['report_id'], "update");
if ($output == "") {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}

$extra_params = GetExtraParams($_GET, "search_key,search_type,choice_filter,sort_by,sort_method,page,label,assign_to,priority,status");
?>
<script language="JavaScript" type="text/javascript">
<!--
function submit()
{
	if (check1() == true) {
		document.form1.submit();
	} else
		return;
}
function check1()
{
	var f=document.form1;
	var y='';
	var message_fix;

	if((f.area.length > 1) && (f.area.options[f.area.selectedIndex].text=='')) {
		y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
		y+='       -<?php echo addslashes($STRING['area'])?><br>';
	}

	var confirm_assign=0;
	if(f.orig_status.value!=f.status.options[f.status.selectedIndex].value){
		if (f.orig_assign_to.value==f.assign_to.options[f.assign_to.selectedIndex].value) {
			confirm_assign=1;
		}
	}
        
	if(y==''){
		if (confirm_assign==1){
			ALEXWANG.Dialog.Show({
				title: '<?php echo addslashes($STRING['edit_report'])?>',
				msg: '<?php echo addslashes($STRING['stauts_changes_assign_to'])?>',
				buttons: ['yes', 'no'],
				width: 300,
				fn: function(button) {
					if (button == 'yes') {
						f.submit();
					} else {
						return false;
					}
				}
			});
			return false;
		}else{
			return OnSubmit(f);
		}
	}else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_report'])?>',
			msg: y,
			buttons: ['ok'],
			width: 300
		});
		return false;
	}
}

// If the status has been changed to fixed, the assign_to would become QC Team
// This function is used by Synology Only
function change_assign_to()
{
	var f=document.form1;

	if( (f.status.value==5) || (f.status.value == 4)){ // 5=Fixed, 4=Re-test
		for (var i=0;i<f.assign_to.length;i++) {
			if (f.assign_to.options[i].text=="QC Team") {
				f.assign_to.options[i].selected=true;
				break;
			}
		}
	}
}
//-->
</script>
<?php
TinyMCEScriptPrint("description");
?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><?php echo htmlspecialchars($project_name)?></a> /
	<?php echo $STRING['edit_report']?>
</div>
<div id="main_container">
		
	<table width="100%" border="0">
		<tr>
			<td align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>">
					<tt class="outline"><?php echo htmlspecialchars($project_name)?></tt>
				</a>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="JavaScript:submit();"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/save.png" border="0" align="middle"><?php echo $STRING['button_submit']?></a>
			</td>
			<td nowrap valign="bottom">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" onsubmit="return check1();" action="report_doupdate.php" name="form1" ENCTYPE="multipart/form-data">
			<input type="hidden" name="report_id" value="<?php echo $_GET['report_id']?>">
			<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
			<input type="hidden" name="choice_filter" value="<?php echo $_GET['choice_filter']?>">
			<input type="hidden" name="search_key" value="<?php echo $_GET['search_key']?>">
			<input type="hidden" name="search_type" value="<?php echo $_GET['search_type']?>">
			<input type="hidden" name="sort_by" value="<?php echo $_GET['sort_by']?>">
			<input type="hidden" name="sort_method" value="<?php echo $_GET['sort_method']?>">
			<input type="hidden" name="label" value="<?php echo $_GET['label']?>">
			<input type="hidden" name="page" value="<?php echo $_GET['page']?>">
<?php
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
