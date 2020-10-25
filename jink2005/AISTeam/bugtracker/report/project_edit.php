<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_edit.php,v 1.21 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_update_project'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!isset($_GET['project_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$userarray = GetAllUsers(0, 0);
// ��o�{�����򥻸��
$project_sql = "select * from ".$GLOBALS['BR_project_table']."  where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "project");
}

$project_name = $project_result->fields["project_name"];
$created_by = $project_result->fields["created_by"];
$owner_name = UidToUsername($userarray, $created_by);
$version_pattern = $project_result->fields["version_pattern"];

?>
<script language="JavaScript" type="text/javascript">
<!--
var apply_count = 0;

function check_field()
{
	if (document.form1.project_name.value.trim() == '') {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_report'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['project_name'], $STRING['no_empty']));?>',
			buttons: ['ok'],
			width: 300
		});
		return false;
	} else {
		return true;
	}

}

function MoveUser(FromList, ToList)
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

function AddUser()
{
	MoveUser(document.form1.alluser, document.form1.allowuser);
}

function RemoveUser()
{
	MoveUser(document.form1.allowuser, document.form1.alluser);
}

function OnLocalSubmit(form)
{
	var select='';
	var AllowUserList = document.form1.allowuser;

	if (apply_count > 0) {
		return false;
	}
	if (check_field() == false) {
		return false
	}
	for (var i=0; i<AllowUserList.length; i++) {
		select = select + AllowUserList.options[i].value + ',';
	}
	document.form1.allow_uid.value = select;
	apply_count++;
	return OnSubmit(form);
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> / <?php echo $STRING['edit_project']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['edit_project']?></tt>
			</td>
			<td nowrap valign="bottom">
				<a href="../index.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="project_doedit.php" name="form1" onsubmit="return OnLocalSubmit(this);">
			<input type="hidden" name="project_id" value="<?php echo $_GET['project_id'];?>">
			<input type="hidden" name="allow_uid">
	
			<table align="center" border="1" cellpadding="0" style="border-collapse: collapse" width="700">
			<tr>
				<td width="100%" class="title" colspan="4">
					<?php echo $STRING['edit']?><?php echo $STRING['colon']?> <a href="project_list.php?project_id=<?php echo $_GET['project_id']?>"><?php echo htmlspecialchars($project_name)?></a>
				</td>
			</tr>
			<tr>
				<td width="160" class="prompt"><?php echo $STRING['project_name'].$STRING['colon']?></td>
				<td width="190" class="content">
					<input class="input-form-text-field" type="text" name="project_name" size="25" value="<?php echo $project_name;?>" maxlength="100">
				</td>
				<td width="160" class="prompt"><?php echo $STRING['area_minor_area'].$STRING['colon']?></td>
				<td class="content" width="190">
					<a href="area_list.php?project_id=<?php echo $_GET['project_id']?>"><?php echo $STRING['area_edit_hint']?></a>
				</td>
			</tr>
			<tr>
				<td class="prompt prompt_align_top">
					<?php echo $STRING['auto_mailto'].$STRING['colon']?>
					<?php PrintTip($STRING['hint_title'], $STRING['auto_mailto_hint']);?>
				</td>
				<td class="content">
<?php
$get_mailto_sql = "select * from ".$GLOBALS['BR_proj_auto_mailto_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and can_unsubscribe!='t'";
$get_mailto_result = $GLOBALS['connection']->Execute($get_mailto_sql) or DBError(__FILE__.":".__LINE__);

$old_mailto_array = array();
while ($row = $get_mailto_result->FetchRow()) {
	$user_id = $row["user_id"];
	array_push($old_mailto_array, $user_id);
}


for ($i=0; $i<6; $i++) {
	echo '
					<select size="1" name="auto_email_'.$i.'">
						<option value=-1></option>';
	$gotuser = 0;
	for ($j=0; $j<sizeof($userarray); $j++) {
		$pos = IsInArray($old_mailto_array, $userarray[$j]->getuserid());
		if (($gotuser == 0) && ($pos >= 0) ) {
			$selected = "selected";
			$old_mailto_array[$pos] = -1;
			$gotuser = 1;
		} else {
			$selected = "";
		}
		$username = $userarray[$j]->getusername();
		if (utf8_strlen($username) > 15) {
			$username = utf8_substr($username, 0, 14)."...";
		}
		echo '
						<option value="'.$userarray[$j]->getuserid().'" '.$selected.'>'.$username.'</option>';
	}
	echo '
					</select><br>';
}

echo '
				</td>
				<td  class="prompt prompt_align_top">'.$STRING['feedback_mailto'].$STRING['colon'];
PrintTip($STRING['hint_title'], $STRING['feedback_mailto_hint']);

echo '
				</td>
				<td class="content">';
$get_feedback_mailto_sql = "select * from ".$GLOBALS['BR_proj_feedback_mailto_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$get_feedback_mailto_result = $GLOBALS['connection']->Execute($get_feedback_mailto_sql) or DBError(__FILE__.":".__LINE__);

$old_feedback_mailto_array = array();
while ($row = $get_feedback_mailto_result->FetchRow()) {
	$user_id = $row["user_id"];
	array_push($old_feedback_mailto_array, $user_id);
}

for ($i=0; $i<6; $i++) {
	echo '
					<select size="1" name="feedback_mailto_'.$i.'">
						<option value=-1></option>';
	$gotuser = 0;
	for ($j=0; $j<sizeof($userarray); $j++) {
		$pos = IsInArray($old_feedback_mailto_array, $userarray[$j]->getuserid());
		if (($gotuser == 0) && ($pos >= 0) ) {
			$selected = "selected";
			$old_feedback_mailto_array[$pos] = -1;
			$gotuser = 1;
		} else {
			$selected = "";
		}
		$show_user = $userarray[$j]->getusername();
		if (utf8_strlen($show_user) > 15) {
			$show_user = utf8_substr($show_user, 0, 14)."...";
		}
		echo '
						<option value="'.$userarray[$j]->getuserid().'" '.$selected.'>'.$show_user.'</option>';
	}
	echo '
					</select><br>';
}
?>
      
				</td>
			</tr>
			<tr>
				<td class="prompt">
<?php
echo $STRING['version_pattern'].$STRING['colon'];
PrintTip($STRING['hint_title'], $STRING['version_pattern_hint']);
?>

				</td>
				<td class="content" colspan="3">
					<input class="input-form-text-field" type="text" maxLength="40" name="version_pattern" value="<?php echo $version_pattern?>" size="30">
				</td>
			</tr>
			<tr>
				<td class="prompt prompt_align_top">
<?php
echo $STRING['accessible_by'].$STRING['colon'];
echo PrintTip($STRING['hint_title'], $STRING['select_hint']);
?>
				</td>
				<td class="content" colspan="3">
					<table border="0" width="100%">
					<tr>
						<td width="40%" align="center" nowrap>
							<?php echo $STRING['all_user_list']?><br>
							<select name="alluser" size="20" multiple>

<?php
$get_access_sql = "select * from ".$GLOBALS['BR_proj_access_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$get_access_result = $GLOBALS['connection']->Execute($get_access_sql) or DBError(__FILE__.":".__LINE__);

$old_access_array = array();
while ($row = $get_access_result->FetchRow()) {
	$user_id = $row["user_id"];
	array_push($old_access_array, $user_id);
}

// �C�X�Ҧ��t�Τ����ϥΪ̥H���I��
for ($i = 0; $i < sizeof($userarray); $i++) {
	$allow_id = $userarray[$i]->getuserid();
	$allow_username = $userarray[$i]->getusername();
	$pos = IsInArray($old_access_array, $allow_id);
	if ($pos >= 0){
		continue;
	}
	if (utf8_strlen($allow_username) > 15) {
		$show_username = utf8_substr($allow_username, 0, 14)."...";
	} else {
		$show_username = $allow_username;
	}
	echo '
								<option value="'.$allow_id.'" title="'.$allow_username.'">'.$show_username.'</option>';
}

?>
				
							</select>
						</td>
						<td width="20%" nowrap align="center">
							<p><button name="add" style="width: 150px;" type="button" onClick="AddUser();">
							<?php echo $STRING['access_add']?></button></p>
							<p><button name="remove" style="width: 150px;" type="button" onClick="RemoveUser();">
							<?php echo $STRING['access_remove']?></button></p>
						</td>
						<td width="40%" nowrap align="center">
							<?php echo $STRING['accessible_user_list']?><br>
							<select size="20" name="allowuser" multiple>
<?php
	$map_array = array();
	for($i=0; $i<sizeof($userarray); $i++) {
		$id = $userarray[$i]->getuserid();
		$name = $userarray[$i]->getusername();
		$map_array[$id] = $name;
	}

	for ($i = 0; $i < sizeof($old_access_array); $i++) {
		$allow_uid = $old_access_array[$i];
		$username = $map_array[$allow_uid];
		if ($username == "") {
			// disabled user
			continue;
		}
		if (utf8_strlen($username) > 15) {
			$show_username = utf8_substr($username, 0, 14)."...";
		} else {
			$show_username = $username;
		}
		echo '
								<option value="'.$allow_uid.'" title="'.$username.'">'.$show_username.'</option>';
	}
?>

							</select>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table> 
			<p align="center"><input type="submit" class="button" value="<?php echo $STRING['button_submit']?>"></p>
		</form>
	</div>
</div>

<?php
include("../include/tail.php"); 
?>
