<?php
/* Copyright c 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_show.php,v 1.19 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/status_function.php");
include("../include/project_function.php");
include("../include/customer_function.php");
include("../include/report_function.php");

AuthCheckAndLogin();

if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
if (!$_GET['report_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}
$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields["project_name"];

$extra_params = GetExtraParams($_GET, "search_key, search_type, choice_filter, sort_by,sort_method,page,label,assign_to,status");

$condition = ConditionByFilterSearch($_GET['choice_filter'], $_GET['label'], $_GET['search_key'], $_GET['search_type']);

// ��o�U�@����ƪ� ID
$get_next_sql = "select report_id from proj".$_GET['project_id']."_report_table 
		where report_id>'".$_GET['report_id']."'";
if ($condition != "") {
	$get_next_sql .= " and (".$condition.")";
}

$get_next_sql .= " order by report_id ASC";

$get_next_result = $GLOBALS['connection']->SelectLimit($get_next_sql, 1, 0) or DBError(__FILE__.":".__LINE__);
$line = $get_next_result->Recordcount();
if ($line == 1) {
	$next_report_id = $get_next_result->fields["report_id"];
} else {
	$next_report_id = "na";
}
  
// ��o�W�@����ƪ� ID
$get_pre_sql = "select report_id from proj".$_GET['project_id']."_report_table
		where report_id<'".$_GET['report_id']."'"; 
if ($condition != "") {
	$get_pre_sql .= " and (".$condition.")";
}
$get_pre_sql .= " order by report_id DESC";

$get_pre_result = $GLOBALS['connection']->SelectLimit($get_pre_sql, 1, 0) or DBError(__FILE__.":".__LINE__);
$line = $get_pre_result->Recordcount();
if ($line == 1) {
	$pre_report_id = $get_pre_result->fields["report_id"];
}else{
	$pre_report_id="na";
}

$output = GetReportOutput($_GET['project_id'], $_GET['report_id'], "");

if ($output == "") {
	WriteSyslog("error", "syslog_not_found", "report", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "report");
}

?>
<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/label.js" type="text/javascript"></script>
<table border="0" width="95%">
	<tr>
		<td width="100%" align="right">
			<a href="report_show_printable.php?project_id=<?php echo $_GET['project_id']?>&report_id=<?php echo $_GET['report_id']?>" target="_blank">
				<?php echo $STRING['view_printable']?>
			</a>
		</td>
	</tr>
</table>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><?php echo htmlspecialchars($project_name)?></a> /
	<?php echo $STRING['show_report']?>
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
			<td width="100%" align="center">
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_manage_label']) {
?>
				<font color="#42649B"><?php echo $STRING['label'].$STRING['colon']?></font>
				<select size="1" id="label_selector" onChange="LabelActionHandler(this);">
					<option value="action" style="color: rgb(119, 119, 119);"><?php echo $STRING['label_actions'];?></option>
					<option value="manage"><?php echo $STRING['label_management']?></option>
					<optgroup id="label_selector_applygroup" label="<?php echo $STRING['apply_label'];?>">
						<option value="new" style="color: rgb(255, 0, 0)"><?php echo $STRING['new_label']?>...</option>
	<?php
	$sql = "select * from ".$GLOBALS['BR_label_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	while($row = $result->FetchRow()) {
		$id = $row["label_id"];
		$name = $row['label_name'];
		echo '
						<option value="'.$id.'">'.$name.'</option>';
	}
	?>
					</optgroup>
					<optgroup id="label_selector_removegroup" label="<?php echo $STRING['remove_label'];?>" style="display:none;"></optgroup>
					
				</select>
<?php
}
?>				
			</td>
<?php

if ($pre_report_id != "na") {
	echo '
			<td nowrap valign="bottom">
				<a href="report_show.php?project_id='.$_GET['project_id'].'&report_id='.$pre_report_id.$extra_params.'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/previous.png" border="0" align="middle">'.$STRING['prev_page'].'
				</a>
			</td>';
}
if ($GLOBALS['Privilege'] & $GLOBALS['can_create_report']) {
	echo '
			<td nowrap valign="bottom">
				<a href="report_new.php?project_id='.$_GET['project_id'].'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/new_report.png" border="0" align="middle">'.$STRING['new_report'].'
				</a>
			</td>';
}
if (($GLOBALS['Privilege'] & $GLOBALS['can_update_report'])) {
	echo '
			<td nowrap valign="bottom">
				<a href="report_update.php?project_id='.$_GET['project_id'].'&report_id='.$_GET['report_id'].$extra_params.'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/update.png" border="0" align="middle">'.$STRING['update'].'
				</a>
			</td>';
}
if ($next_report_id!="na") {
	echo '
			<td nowrap valign="bottom">
				&nbsp;&nbsp;<a href="report_show.php?project_id='.$_GET['project_id'].'&report_id='.$next_report_id.$extra_params.'">
				<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/next.png" border="0" align="middle">'.$STRING['next_page'].'
				</a>
			</td>';
}

?>
			<td nowrap valign="bottom">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	
	<div id="sub_container" style="width: 98%;">
		
		<!-- For label operation -->
		<table border="0" width="700" align="center">
			<tr>
				<td>
					<div id="main_subject_container<?php echo $_GET['report_id']?>" style="width:700px;display:block;"><?php PrintLabel($_GET['project_id'], $_GET['report_id']);?>
						<div id="fake_subject"></div>
					</div>
				</td>
			</tr>
		</table>
		<!-- end of for label operation -->
	
<?php
echo $output;

?>
	</div>
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>&nbsp;</td>
<?php

if ($pre_report_id != "na") {
	echo '
			<td nowrap valign="bottom">
				<a href="report_show.php?project_id='.$_GET['project_id'].'&report_id='.$pre_report_id.$extra_params.'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/previous.png" border="0" align="middle">'.$STRING['prev_page'].'
				</a>
			</td>';
}
if ($GLOBALS['Privilege'] & $GLOBALS['can_create_report']) {
	echo '
			<td nowrap valign="bottom">
				<a href="report_new.php?project_id='.$_GET['project_id'].'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/new_report.png" border="0" align="middle">'.$STRING['new_report'].'
				</a>
			</td>';
}
if (($GLOBALS['Privilege'] & $GLOBALS['can_update_report'])) {
	echo '
			<td nowrap valign="bottom">
				<a href="report_update.php?project_id='.$_GET['project_id'].'&report_id='.$_GET['report_id'].$extra_params.'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/update.png" border="0" align="middle">'.$STRING['update'].'
				</a>
			</td>';
}
if ($next_report_id!="na") {
	echo '
			<td nowrap valign="bottom">
				&nbsp;&nbsp;<a href="report_show.php?project_id='.$_GET['project_id'].'&report_id='.$next_report_id.$extra_params.'">
				<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/next.png" border="0" align="middle">'.$STRING['next_page'].'
				</a>
			</td>';
}

?>
			<td nowrap valign="bottom">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

<?php
// ��� COPY TO �����   

$project_array = GetAllProjects($_SESSION[SESSION_PREFIX.'uid']);
$project_option = "";
for ($i = 0; $i < sizeof($project_array); $i++) {
	$visible_project_id = $project_array[$i]->getprojectid();
	if ($visible_project_id == $_GET['project_id']) {
		continue;
	}
	$visiable_project_name = $project_array[$i]->getprojectname();
	$project_option .= '
			<option value="'.$visible_project_id.'">'.$visiable_project_name.'</option>';
}
if ($project_option != "") {
	echo '
	<form method="POST" action="report_copyto.php">
	<p align="center">
		<font color="#000066">'.$STRING['copy_to'].$STRING['colon'].'</font>
		<select size="1" name="copyto_project_id">
		';
	echo $project_option;
	echo '
		</select>
		<input type="hidden" name="project_id" value="'.$_GET['project_id'].'">
		<input type="hidden" name="report_id" value="'.$_GET['report_id'].'">
		<input type="hidden" name="choice_filter" value="'.$_GET['choice_filter'].'">
		<input type="submit" value="'.$STRING['button_go'].'" name="B1" class="button">
	</p>
	</form>';
}

echo '
</div>';
if ($GLOBALS['Privilege'] & $GLOBALS['can_manage_label']) {
?>
<script language="JavaScript" type="text/javascript">
ALEXWANG.LabelHandler.Init({
	project_id: <?php echo $_GET['project_id']?>,
	bugids: [<?php echo $_GET['report_id']?>],
	checkbox_prefix: false,
	container_prefix: 'main_subject_container',
	label_color: <?php PrintLabelColorArray($_GET['project_id'])?>,
	label_selector: 'label_selector'
});
</script>
<?php
}
PrintGotoTop();
include("../include/tail.php");
?>
