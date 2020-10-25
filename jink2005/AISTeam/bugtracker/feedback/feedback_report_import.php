<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_report_import.php,v 1.30 2013/07/07 21:25:52 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/status_function.php");
include("../include/project_function.php");
include("../include/customer_function.php");
include("../include/area_js.php");
include("../include/tinymce.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_feedback'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['project_id'])) {
	$_GET['project_id'] = $_SESSION[SESSION_PREFIX.'back_array']['project_id'];
}

if ($_GET['project_id'] == "") {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (!isset($_GET['report_id']) || ($_GET['report_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "report_id");
}

if (!isset($_GET['from'])) {
	$_GET['from'] = $_SESSION[SESSION_PREFIX.'back_array']['finish_from'];
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

$feedback_system_sql = "select import_description from ".$GLOBALS['BR_feedback_config_table'];
$feedback_system_result = $GLOBALS['connection']->Execute($feedback_system_sql) or DBError(__FILE__.":".__LINE__);
$line = $feedback_system_result->Recordcount();
if ($line != 1) {
	DBError(__FILE__.":".__LINE__);
}
$import_description = $feedback_system_result->fields["import_description"];
   
$userarray = GetAllUsers(1, 1);
$statusarray = GetStatusArray();
	
// ��o���\�ϥΪ̨ϥΪ� status �W��
$group_status_sql = "select * from ".$GLOBALS['BR_group_allow_status_table']." where group_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'gid']);
$group_status_result = $GLOBALS['connection']->Execute($group_status_sql) or DBError(__FILE__.":".__LINE__);
$allow_status_array = array();
while ($row = $group_status_result->FetchRow()) {
	$status_id = $row['status_id'];
	array_push($allow_status_array, $status_id);
}

// Get feedback data
$report_sql = "select * from proj".$_GET['project_id']."_feedback_table 
				where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id']);
$report_result = $GLOBALS['connection']->Execute($report_sql) or DBError(__FILE__.":".__LINE__);
$line = $report_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "report");
}

$summary = $report_result->fields["summary"];
$customer_id = $report_result->fields["customer_id"];
$cust_report_id = $report_result->fields["cust_report_id"];
$type = $report_result->fields["type"];
$priority = $report_result->fields["priority"];
$status = $report_result->fields["status"];
$version = $report_result->fields["version"];
$reproducibility = $report_result->fields["reproducibility"];

$log_sql = "select * from proj".$_GET['project_id']."_feedback_content_table 
			where report_id=".$GLOBALS['connection']->QMagic($_GET['report_id'])." order by content_id ASC";
$log_result = $GLOBALS['connection']->Execute($log_sql) or DBError(__FILE__.":".__LINE__);
$log = "";
while ($row = $log_result->FetchRow()) {
	$content_id = $row['content_id'];
	$customer_email = $row['customer_email'];
	$internal_user_id = $row['internal_user_id'];
	$internal_username = UidTOUsername($userarray, $row['internal_user_id']);
	$post_time = $log_result->UserTimeStamp($row['post_time'], GetDateTimeFormat());
	$filename = $row['filename'];
	$description = $row['description'];

	if ($customer_email != "") {
		$log .= "<p><b>[$customer_email]</b> reported at $post_time";
	} else {
		$log .= "<p><b>[$internal_username]</b> reported at $post_time";
	}
			
	if ($filename != "") {
		$log .= ", upload file: <a href=\"feedback_report_download.php?project_id=".$_GET['project_id']."&content_id=".$content_id."\" target=\"_blank\">";
		$log .= "<img border=\"0\" src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/file.gif\" title=\"download\">";
		$log .="</a>";
	}
	$log .= "</p>".$description."<hr>";
}
$extra_params = GetExtraParams($_GET, "search_key,customer_filter,page,sort_by,sort_method");

TinyMCEScriptPrint("description");
?>
<script language="JavaScript">
<!--
function check1()
{
    var f=document.form1;
    var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
	if(!f.summary.value){y+='       -<?php echo addslashes($STRING['summary'])?><br>';}
	if((f.area.length > 1) && (f.area.options[f.area.selectedIndex].text=='')) {
		y+='       -<?php echo addslashes($STRING['area'])?>\n';
	}

    if(y=='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>'){
        return OnSubmit(f);
    } else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['import'])?>',
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
	<a href="<?php echo $GLOBALS['SYS_URL_ROOT']?>/index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="feedback_list.php?project_id=<?php echo $_GET['project_id'].$extra_params?>"><?php echo htmlspecialchars($project_name)." ".$STRING['title_feedback']?></a> /
	<?php echo $STRING['import']?>
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
			<td nowrap valign="bottom">
				<a href="<?php echo $_GET['from']?>?project_id=<?php echo $_GET['project_id']?>&report_id=<?php echo $_GET['report_id'].$extra_params?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="../report/report_donew.php" onsubmit="return check1();" name="form1" ENCTYPE="multipart/form-data">
		<table align="center" border="1" style="border-collapse: collapse" width="700" cellpadding="2" cellspacing="0">
		<tr>
			<td class="title" colspan="4" align="center"><?php echo $STRING['import']?>
				<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
				<input type="hidden" name="report_id" value="<?php echo $_GET['report_id']?>">
				<input type="hidden" name="feedback_report_id" value="<?php echo $_GET['report_id']?>">
				<input type="hidden" name="back_from" value="<?php echo $_SERVER['PHP_SELF']?>">
				<input type="hidden" name="finish_from" value="<?php echo $_GET['from']?>">
			</td>
		</tr>
		<tr>
			<td width="160" class="prompt" nowrap>
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['summary']?>
			</td>
			<td width="540" class="content" colspan="3">
				<input class="input-form-text-field" type="text" name="summary" size="78" value="<?php echo $summary?>" maxlength="200"></td>
		</tr>
		<tr>
			<td width="160" class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['type']?>
			</td>
			<td width="190" class="content">
			<p><select size="1" name="type">
<?php
	for ($i=1; $i<sizeof($GLOBALS['type_array']); $i++) {
		if ($type == $i) {
			echo "<option selected value=\"$i\">".$STRING[$GLOBALS['type_array'][$i]]."</option>";
		} else {
			echo "<option value=\"$i\">".$STRING[$GLOBALS['type_array'][$i]]."</option>";
		}
		
	}
?>
			</select></p>
			</td>
			<td class="prompt" width="160">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['version']?>
			</td>
			<td width="190" class="content">
				<input class="input-form-text-field" type="text" name="version" value="<?php echo $version?>" maxlength="40">
			</td>
		</tr>
		<tr>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['priority']?>
			</td>
			<td class="content"><select size="1" name="priority">
<?php
	echo "<option value=\"0\">".$STRING[$GLOBALS['priority_array'][0]]."</option>";
	for ($i = (sizeof($GLOBALS['priority_array']) - 1); $i > 0; $i--) {           
		if ($priority == $i) {
			echo "<option value=\"$i\" selected>".$STRING[$GLOBALS['priority_array'][$i]]."</option>";
		} else {
			echo "<option value=\"$i\">".$STRING[$GLOBALS['priority_array'][$i]]."</option>";
		}
	}
?>
				</select>
			</td>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['status']?>
			</td>
			<td class="content"><select size="1" name="status">
        <?php
	for ($i=0; $i<sizeof($statusarray); $i++) {
		$status_id = $statusarray[$i]->getstatusid();
		$status_name = $statusarray[$i]->getstatusname();

		// Admin ��ܩҦ��� status
		if ($_SESSION[SESSION_PREFIX.'gid'] == 0) {
			if ($status_name=="New") {
				echo "<option value=\"$status_id\" SELECTED>$status_name</option>";
			} else {
				echo "<option value=\"$status_id\">$status_name</option>";
			}
		// ��L�H�h�̨���ݸs�եi�ϥΪ� status �����
		} else {
			if (IsInArray($allow_status_array, $status_id) == -1) {
				continue;
			} else {
				if ($status_name=="New"){
					echo "<option  value=\"".$status_id."\" selected>".$status_name."</option>";
				}else{
					echo "<option  value=\"".$status_id."\">".$status_name."</option>";
				}
			}
		}
	}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['assign_to']?></font>
			</td>
			<td class="content">
				<select size="1" name="assign_to">
				<option value="-1" SELECTED> </option>
<?php
	for ($i=0;$i<sizeof($userarray);$i++) {
		$user_id = $userarray[$i]->getuserid();
		if (($user_id == 0) || ($userarray[$i]->getdisabled())){
			continue;
		}
		if (CheckProjectAccessable($_GET['project_id'], $user_id) == FALSE) {
			continue;
		}
		echo "<option value=\"".$user_id."\">".$userarray[$i]->getusername()."</option>";
	}
?>
        
				</select>
			</td>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['area_minor_area']?>
			</td>
			<td class="content">
<?php
	$all_area_sql = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and area_parent=0 order by area_name";
	$root_area_result = $GLOBALS['connection']->Execute($all_area_sql) or DBError(__FILE__.":".__LINE__);
	$root_area_line = $root_area_result->Recordcount();
	// �p�G���]�w Area �� Minor Area ���ܡA�N�C�X�U�Ԧ����H�ѿ��
	// �p�G�S���h��X��r����H�ѿ�J
	if($root_area_line!=0){
		echo "<select size=\"1\" name=\"area\" onChange=\"AreaChange()\"><option></option>";
		while ($root_area_row = $root_area_result->FetchRow()){
			$area_name = $root_area_row["area_name"];
			echo "<option>$area_name</option>";
		}
		echo "</select>/<select size=\"1\" name=\"minor_area\" onChange=\"UpdateAssignTo()\"></select>";
	}else{
		echo '
				<input class="input-form-text-field" type="text" name="area" size="10" maxlength="40">/
				<input class="input-form-text-field" type="text" name="minor_area" size="10" maxlength="40">';
	}
?>
         
			</td>
		</tr>
		<tr>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['reproducibility']?>
			</td>
			<td class="content">
			<select size="1" name="reproducibility">
<?php
	for ($i=0;$i<sizeof($GLOBALS['reproducibility_array']);$i++) {
		if ($reproducibility == $i) {
			echo "<option value=\"$i\" selected>".$STRING[$GLOBALS['reproducibility_array'][$i]]."</option>";
		} else {
			echo "<option value=\"$i\">".$STRING[$GLOBALS['reproducibility_array'][$i]]."</option>";
		}
	}
?>
				</select>
			</td>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['reported_by_customer']?>
			</td>
			<td class="content">
<?php	 
	echo '<input type="hidden" name="reported_by_customer" value="'.$customer_id.'">';
	$customer_array = GetAllCustomers();
	for ($i = 0; $i<sizeof($customer_array); $i++) {
        if ($customer_id == $customer_array[$i]->getcustomerid()) {
			echo $customer_array[$i]->getcustomername();
		}
	}
?>
			</td>
		</tr>
		<tr>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['file_upload']?>
			</td>
			<td colspan="3" class="content">
				<input class="input-form-text-field" type="file" name="file" size="55" class="button">
				<?php PrintTip($STRING['hint_title'], $STRING['file_upload_hint'].get_cfg_var("upload_max_filesize"));?>
			</td>
		</tr>
		<tr>
			<td class="prompt prompt_align_top">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['logs']?>
			</td>
			<td colspan="3" class="content">
<?php
	echo $log;
	echo '<font color="red">';
	echo $STRING['import_notice'];
	echo '</font>';
?>
			</td>
		</tr>
		<tr>
			<td class="prompt prompt_align_top">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['description'].
				PrintTip($STRING['hint_title'], $STRING['description_hint'], "return")?>
			</td>
			<td colspan="3" class="content">
				<textarea rows="16" name="description" style="width:100%"><?php echo $_SESSION[SESSION_PREFIX.'back_array']['description']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="prompt prompt_align_top">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['import_description'].
				PrintTip($STRING['hint_title'], $STRING['import_description_hint'], "return")?>
			</td>
			<td colspan="3" class="content">
				<textarea class="input-form-text-textarea" rows="10" name="feedback_description" id="feedback_description" style="width:100%"><?php echo $import_description?></textarea>
			</td>
		</tr>
		</table>
		<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" name="B1" class="button"></p>
		</form>
	</div>
</div>

<?php
PrintGotoTop();
include("../include/tail.php");
?>
