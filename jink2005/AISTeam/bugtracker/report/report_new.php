<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: report_new.php,v 1.25 2013/07/05 22:41:04 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/status_function.php");
include("../include/project_function.php");
include("../include/customer_function.php");

AuthCheckAndLogin();

include("../include/area_js.php");
include("../include/tinymce.php");

if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_report'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
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
$version_pattern = $project_result->fields["version_pattern"];

$userarray = GetAllUsers(0, 0);
$statusarray = GetStatusArray();

// ��o���\�ϥΪ̨ϥΪ� status �W��
$group_status_sql = "select * from ".$GLOBALS['BR_group_allow_status_table']." where group_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'gid']);
$group_status_result = $GLOBALS['connection']->Execute($group_status_sql) or DBError(__FILE__.":".__LINE__);
$allow_status_array = array();
while ($row = $group_status_result->FetchRow()) {
	$status_id = $row['status_id'];
	array_push($allow_status_array, $status_id);
}

?>
<script language="JavaScript" type="text/javascript">
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
		title: '<?php echo addslashes($STRING['new_report'])?>',
		msg: y,
		buttons: ['ok'],
		width: 300
		});
        return false;
    }
	return false;
}
//-->
</script>
<?php
TinyMCEScriptPrint("description");
?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>"><?php echo htmlspecialchars($project_name)?></a> /
	<?php echo $STRING['new_report']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>">
					<tt class="outline"><?php echo htmlspecialchars($project_name)?></tt>
				</a>
			</td>
			<td nowrap valign="bottom">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="report_donew.php" onsubmit="return check1();" name="form1" ENCTYPE="multipart/form-data">
		<table border="1" align="center" style="border-collapse: collapse" width="700" cellpadding="2" cellspacing="0">
		<tr>
			<td class="title" colspan="4" align="center"><?php echo $STRING['new_report']?>
				<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
			</td>
		</tr>
		<tr>
			<td width="140" class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['summary']?>
			</td>
			<td width="560" class="content" colspan="3">
				<input class="input-form-text-field" type="text" name="summary" size="78" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['summary']?>" maxlength="200">
			</td>
		</tr>
		<tr>
			<td width="140" class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['type']?>
			</td>
			<td width="210" class="content">
				<select size="1" name="type">
<?php
for ($i=1; $i<sizeof($GLOBALS['type_array']); $i++) {
	if ($_SESSION[SESSION_PREFIX.'back_array']['type'] == $i) {
		echo '
					<option selected value="'.$i.'">'.$STRING[$GLOBALS['type_array'][$i]].'</option>';
	} else {
		echo '
					<option value="'.$i.'">'.$STRING[$GLOBALS['type_array'][$i]].'</option>';
	}
	
}
?>

				</select>
			</td>
			<td class="prompt" width="140">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['version']?>
			</td>
			<td width="210" class="content">
<?php
// �p�G�S���]�w version_pattern ����
// �N��ܤ@�Ӥ�r���A��ϥΪ̦ۦ��J version
if ($version_pattern == "") {
	echo '<input class="input-form-text-field" type="text" name="version" value="'.$_GET['version'].'" maxlength="40">';
} else {
	for ($i = 0; $i <= strlen($version_pattern); $i++) {
		if ($version_pattern{$i} == '%') {
			echo '<select size="1" name="version'.$i.'">';
			echo '<option value="-1"></option>';
			for ($j = 0; $j <= 9; $j++) {
				echo '<option value="'.$j.'">'.$j.'</option>';
			}
			echo '</select>';
		} else if ($version_pattern{$i} == '@') {
			echo '<select size="1" name="version'.$i.'">';
			echo '<option value="-1"></option>';
			for ($j = ord("a"); $j <= ord("z"); $j++) {
				echo '<option value="'.chr($j).'">'.chr($j).'</option>';
			}
			echo '</select>';
		} else {
			echo " <b>".$version_pattern{$i}."</b> ";
		}
	}
}
	
?>
			</td>
		</tr>
		<tr>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['priority']?>
			</td>
			<td class="content">
				<select size="1" name="priority">
<?php
echo '
					<option value="0">'.$STRING[$GLOBALS['priority_array'][0]].'</option>';
for ($i = (sizeof($GLOBALS['priority_array']) - 1); $i > 0; $i--) {           
	if (isset($_SESSION[SESSION_PREFIX.'back_array']['priority']) && ($_SESSION[SESSION_PREFIX.'back_array']['priority'] == $i)) {
		echo '<option value="'.$i.'" selected>'.$STRING[$GLOBALS['priority_array'][$i]].'</option>';
	} else {
		echo '<option value="'.$i.'">'.$STRING[$GLOBALS['priority_array'][$i]].'</option>';
	}
 }
?>
				</select>
			</td>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['status']?>
			</td>
			<td class="content">
				<select size="1" name="status">
<?php
for ($i=0; $i<sizeof($statusarray); $i++) {
	$status_id = $statusarray[$i]->getstatusid();
	$status_name = htmlspecialchars($statusarray[$i]->getstatusname());

	// Admin ��ܩҦ��� status
	if ($_SESSION[SESSION_PREFIX.'gid'] == 0) {
		if (isset($_SESSION[SESSION_PREFIX.'back_array']['status'])) {
			if ($status_id == $_SESSION[SESSION_PREFIX.'back_array']['status']) {
				echo '<option value="'.$status_id.'" selected>'.$status_name.'</option>';
			} else {
				echo '<option value="'.$status_id.'">'.$status_name.'</option>';
			}
		} else {
			if ($status_name=="New") {
				echo '<option value="'.$status_id.'" selected>'.$status_name.'</option>';
			} else {
				echo '<option value="'.$status_id.'">'.$status_name.'</option>';
			}
		}
	// ��L�H�h�̨���ݸs�եi�ϥΪ� status �����
	} else {
		if (IsInArray($allow_status_array, $status_id) == -1) {
			continue;
		} else {
			if ($status_name=="New"){
				echo '<option value="'.$status_id.'" selected>'.$status_name.'</option>';
			}else{
				echo '<option value="'.$status_id.'">'.$status_name.'</option>';
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
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['assign_to']?>
			</td>
			<td class="content">
				<select size="1" name="assign_to">
					<option value="-1" SELECTED> </option>
<?php
for ($i=0;$i<sizeof($userarray);$i++) {
	$user_id = $userarray[$i]->getuserid();
	if (CheckProjectAccessable($_GET['project_id'], $user_id) == FALSE) {
		continue;
	}
	if ( (isset($_SESSION[SESSION_PREFIX.'back_array']['assign_to'])) && ($_SESSION[SESSION_PREFIX.'back_array']['assign_to'] == $user_id) ) {
		echo '<option value="'.$user_id.'" selected>'.$userarray[$i]->getusername().'</option>';
	} else {
		echo '<option value="'.$user_id.'">'.$userarray[$i]->getusername().'</option>';
	}
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
if($root_area_line != 0) {
	echo '<select size="1" name="area" onChange="AreaChange()"><option></option>';
	while ($root_area_row = $root_area_result->FetchRow()){
		$area_name = $root_area_row["area_name"];
		echo '<option>'.$area_name.'</option>';
	}
	echo '</select>/<select size="1" name="minor_area" onChange="UpdateAssignTo()"></select>';
}else{
	echo '<input class="input-form-text-field" type="text" name="area" value="'.$_SESSION[SESSION_PREFIX.'back_array']['area'].'" size="10" maxlength="40">/';
	echo '<input class="input-form-text-field" type="text" name="minor_area" value="'.$_SESSION[SESSION_PREFIX.'back_array']['minor_area'].'" size="10" maxlength="40">';
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
	if ($i == $_SESSION[SESSION_PREFIX.'back_array']['reproducibility']) {
		echo '<option value="'.$i.'" selected>'.$STRING[$GLOBALS['reproducibility_array'][$i]].'</option>';
	} else {
		echo '<option value="'.$i.'">'.$STRING[$GLOBALS['reproducibility_array'][$i]].'</option>';
	}
	
}
?>
				</select>
			</td>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['reported_by_customer']?>
			</td>
			<td class="content">
					<select size="1" name="reported_by_customer">
						<option value="-1"> </option>
<?php
$visible_sql="select customer_id from ".$GLOBALS['BR_proj_customer_access_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$visible_result = $GLOBALS['connection']->Execute($visible_sql) or DBError(__FILE__.":".__LINE__); 
$visible_customers = array();
while ($row = $visible_result->FetchRow()) {
	array_push($visible_customers, $row['customer_id']);
}
$customer_array = GetAllCustomers();
for ($i = 0; $i<sizeof($customer_array); $i++) {
	if (-1 == IsInArray($visible_customers, $customer_array[$i]->getcustomerid())) {
		continue;
	}
	$customer_name = $customer_array[$i]->getcustomername();
	
	if (utf8_strlen($customer_name) > 50) {
		$customer_name = substr($customer_name, 0, 50)."...";
	}
	if ($_SESSION[SESSION_PREFIX.'back_array']['reported_by_customer'] == $customer_array[$i]->getcustomerid()) {
		echo '<option value="'.$customer_array[$i]->getcustomerid().'" selected>';
	} else {
		echo '<option value="'.$customer_array[$i]->getcustomerid().'">';
	}
	
	echo $customer_name.$len;
	echo '</option>';
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="prompt">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9"><?php echo $STRING['file_upload']?>
			</td>
			<td colspan="3" class="content">
				<input class="input-form-text-field" type="file" name="file" size="55">
				<?php PrintTip($STRING['hint_title'], $STRING['file_upload_hint'].get_cfg_var("upload_max_filesize"));?>
			</td>
		</tr>
		<tr>
			<td class="prompt prompt_align_top">
				<img border="0" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/triangle_s.gif" width="8" height="9">
				<?php echo $STRING['description'].PrintTip($STRING['hint_title'], $STRING['description_hint'], "return")?>

			</td>
			<td colspan="3" class="content">
				<textarea rows="18" name="description" style="width:100%"><?php echo $_SESSION[SESSION_PREFIX.'back_array']['description']?></textarea>
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
