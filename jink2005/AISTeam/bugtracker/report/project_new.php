<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_new.php,v 1.14 2010/07/26 09:05:26 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_create_project'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
$userarray = GetAllUsers(0, 0);
?>
<script language="JavaScript" type="text/javascript">
<!--
var apply_count = 0;

function check_field()
{
	if (document.form1.project_name.value.trim() == '') {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_report'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['project_name'], $STRING['no_empty']));?>',
			buttons: ['ok'],
			width: 300
		});
		return false
	}
	return true;
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
		return false;
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
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> / <?php echo $STRING['new_project']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['new_project']?></tt>
			</td>
			<td nowrap valign="bottom">
				<a href="../index.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<form method="POST" action="project_donew.php" name="form1" onSubmit="return OnLocalSubmit(this);">
			<input type="hidden" name="allow_uid">
		
			<table align="center" border="1" cellpadding="0" style="border-collapse: collapse" width="700">
			<tr>
				<td class="title" colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td class="prompt" width="160"><?php echo $STRING['project_name'].$STRING['colon']?></td>
				<td class="content" colspan="3">
					<p><input class="input-form-text-field" type="text" name="project_name" size="30" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['project_name']?>" maxlength="100"></p>
				</td>
			</tr>
			<tr>
				<td class="prompt prompt_align_top" width="160">
					<?php echo $STRING['auto_mailto'].$STRING['colon']?>
					<?php echo PrintTip($STRING['hint_title'], $STRING['auto_mailto_hint'])?>
				</td>
				<td class="content" width="190">

<?php
	
for ($i=0; $i<6; $i++) {
	echo '
					<select size="1" name="auto_email_'.$i.'">
						<option value=-1></option>';
	for ($j=0; $j<sizeof($userarray); $j++) {
		$username = $userarray[$j]->getusername();
		if (utf8_strlen($username) > 15) {
			$username = utf8_substr($username, 0, 14)."...";
		}
		if ( (isset($_SESSION[SESSION_PREFIX.'back_array']['auto_email_'.$i])) &&
			 ($_SESSION[SESSION_PREFIX.'back_array']['auto_email_'.$i] == $userarray[$j]->getuserid()) ) {
			echo '
						<option value="'.$userarray[$j]->getuserid().'" selected>'.$username.'</option>';
		} else {
			echo '
						<option value="'.$userarray[$j]->getuserid().'">'.$username.'</option>';
		}
	}
	echo '
					</select><br>';
}
?>
				</td>
				<td class="prompt prompt_align_top" width="160">
					<?php echo $STRING['feedback_mailto'].$STRING['colon']?>
					<?php echo PrintTip($STRING['hint_title'], $STRING['feedback_mailto_hint'])?>
				</td>
				<td class="content" width="190">

<?php

for ($i=0; $i<6; $i++) {
	echo '
					<select size="1" name="feedback_mailto_'.$i.'">
						<option value=-1></option>';
	for ($j=0; $j<sizeof($userarray); $j++) {
		$username = $userarray[$j]->getusername();
		if (utf8_strlen($username) > 15) {
			$username = utf8_substr($username, 0, 14)."...";
		}
		if ( (isset($_SESSION[SESSION_PREFIX.'back_array']['feedback_mailto_'.$i])) &&
			 ($_SESSION[SESSION_PREFIX.'back_array']['feedback_mailto_'.$i] == $userarray[$j]->getuserid()) ) {
			echo '
						<option value="'.$userarray[$j]->getuserid().'" selected>'.$username.'</option>';
		} else {
			echo '
						<option value="'.$userarray[$j]->getuserid().'">'.$username.'</option>';
		}
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
PrintTip($STRING['hint_title'],$STRING['version_pattern_hint']);
?>
				</td>
				<td class="content" colspan="3">
					<input class="input-form-text-field" type="text" maxLength="40" name="version_pattern" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['version_pattern']?>" size="30">
				</td>
			</tr>
			<tr>
				<td class="prompt prompt_align_top">
<?php
echo $STRING['accessible_by'].$STRING['colon'];
PrintTip($STRING['hint_title'], $STRING['select_hint']);
?>
				<td class="content" colspan="3">
					<table border="0" width="100%" align="left">
					<tr>
						<td width="40%" nowrap align="center">
							<?php echo $STRING['all_user_list']?><br>
							<select size="20" name="alluser" multiple>

<?php
$allow_array = explode(",", $_SESSION[SESSION_PREFIX.'back_array']['allow_uid']);
// �C�X�Ҧ��t�Τ����ϥΪ̥H���I��n��J�ϥΪ̦C�?�H
for ($i=0; $i<sizeof($userarray); $i++) {
	$allow_username = $userarray[$i]->getusername();
	$allow_id = $userarray[$i]->getuserid();
	
	if (IsInArray($allow_array, $allow_id) != -1) {
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
						<td width="20%" nowrap align="center" nowrap>
							<p><button name="add" style="width: 150px;" type="button" onClick="AddUser();">
							<?php echo $STRING['access_add']?></button></p>
							<p><button name="remove" style="width: 150px;" type="button" onClick="RemoveUser();">
							<?php echo $STRING['access_remove']?></button></p>
						</td>
						<td width="40%" nowrap align="center">
							<?php echo $STRING['accessible_user_list']?><br>
							<select size="20" name="allowuser" multiple>
<?php

for ($i = 0; $i < sizeof($allow_array); $i++) {
	$allow_uid = $allow_array[$i];
	$username = UidToUsername($userarray, $allow_uid);
	if ($username == "") {
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
			<p align="center"><input type="submit" name="B1" value="<?php echo $STRING['button_create']?>" class="button"></p>
		</form>
	</div>
</div>



<?php
include("../include/tail.php");
?>
