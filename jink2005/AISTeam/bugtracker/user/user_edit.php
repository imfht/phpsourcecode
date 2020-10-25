<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_edit.php,v 1.14 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!isset($_GET['user_id'])) {
	$_GET['user_id'] = $_SESSION[SESSION_PREFIX.'uid'];
}

if ($_SESSION[SESSION_PREFIX.'uid'] != $_GET['user_id']) {
	if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_privilege");
	}
} else {
	if (!($GLOBALS['Privilege'] & $GLOBALS['can_edit_selfdata'])) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_privilege");
	}
}

$user_sql = "select * from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id']);
$user_result = $GLOBALS['connection']->Execute($user_sql) or DBError(__FILE__.":".__LINE__);
$line = $user_result->Recordcount();
if ($line!=1) {
	ErrorPrintOut("no_such_xxx", "user");
}
$username = $user_result->fields["username"];
$email = $user_result->fields["email"];
$group_id = $user_result->fields["group_id"];
$realname = $user_result->fields["realname"];
$account_disabled = $user_result->fields["account_disabled"];
$language = $user_result->fields["language"];
$password = $user_result->fields["password"];
if ($password == md5("")) {
	$show_password = "";
	$verify_password = "";
} else {
	$show_password = "12345678";
	$verify_password = "AlexWang";
}

$project_array = GetAllProjects();
   
?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.edituser;
	var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';

	if ( f.group_id.options && (f.group_id.options[f.group_id.selectedIndex].value==-1) ) {
		y+='-<?php echo addslashes($STRING['group_name'])?><br>';
	}
	if(!f.email.value){y+='-<?php echo addslashes($STRING['email'])?><br>';}

<?php
if (($SYSTEM['auth_method'] == "native") || ($_GET['user_id'] == 0)) {
?>
	if (!((f.password1.value == '12345678') && (f.password2.value == 'AlexWang'))) {
		if(f.password1.value!=f.password2.value) {y+='<br><?php echo addslashes($STRING['password_not_match'])?>';}
	}
<?php
}
?>
	if(y=='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>'){
		return OnSubmit(f);
	}else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_user'])?>',
			msg: y,
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
}
function check_access_all()
{
	var checkall;
	if (document.edituser.checkall.checked) {
		checkall = true;
	} else {
		checkall = false;
	}
<?php
	for ($i=0; $i<sizeof($project_array); $i++) {
		echo "if (document.edituser.project".$i.") { \n";
		echo "	document.edituser.project".$i.".checked=checkall;";
		echo "} \n";
	}
?>
	return true;
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user']) {
	echo '
	<a href="../system/system.php">'.$STRING['title_system'].'</a> /
	<a href="user_admin.php">'.$STRING['user_management'].'</a> /
	'.$STRING['edit_user'];
} else {
	echo '
	<a href="../system/system.php">'.$STRING['title_system'].'</a> /
	'.$STRING['my_account'];
}
?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_user.png" width="48" height="48" align="middle" border="0">
				<tt class="outline">
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user']) {
	echo $STRING['edit_user'];
} else {
	echo $STRING['my_account'];
}
?>
				</tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user']) {
	echo '<a href="user_admin.php">';
} else {
	echo '<a href="../system/system.php">';
}
?>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<form method="POST" name="edituser" action="user_doedit.php" onSubmit="return check1();">
			<input type="hidden" name="user_id" value="<?php echo $_GET['user_id']?>">
			<input type="hidden" name="user_type" value="<?php echo $_GET['user_type']?>">

			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>
			
				<table class="table-input-form" align="center">
				<tr>
					<td class="item_prompt_small" width="33%">
						<?php echo $STRING['username'].$STRING['colon']?>
					</td>
					<td width="67%">
						<?php echo $username?>
					</td>
				</tr>    
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['group_name'].$STRING['colon']?>
					</td>
					<td>
<?php
if ($_GET['user_id'] == $_SESSION[SESSION_PREFIX.'uid']) {
	$group_sql = "select group_name from ".$GLOBALS['BR_group_table']." where group_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'gid']);
	$group_result = $GLOBALS['connection']->Execute($group_sql) or DBError(__FILE__.":".__LINE__);
	$line = $group_result->Recordcount();

	if ($line!=1) {
		ErrorPrintOut("no_such_xxx", "group");
	}

	$group_row = $group_result->FetchRow();
	echo $group_row["group_name"];
	echo '
						<input type="hidden" name="group_id" value="'.$_SESSION[SESSION_PREFIX.'gid'].'">';
} else {
	echo '
						<select size="1" name="group_id">
							<option value="-1"></optopn>';

	$group_sql = "select * from ".$GLOBALS['BR_group_table']." order by group_name";
	$group_result = $GLOBALS['connection']->Execute($group_sql) or DBError(__FILE__.":".__LINE__);
	while ($all_group_row = $group_result->FetchRow()) {
		$the_group_id = $all_group_row["group_id"];
		$the_group_name = htmlspecialchars($all_group_row["group_name"]);
		if ($the_group_id==$group_id) {
			echo '
							<option value="'.$the_group_id.'" selected>'.$the_group_name.'</option>';
		}else{
			echo '
							<option value="'.$the_group_id.'">'.$the_group_name.'</option>';
		}
	}
	echo '
						</select>';
}
?>

					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['real_name'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" type="text" name="realname" size="40" value="<?php echo $realname?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['email'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" type="text" name="email" size="40" value="<?php echo $email?>" maxlength="50">
					</td>
				</tr>
<?php
if (($SYSTEM['auth_method']== "native") || ($_GET['user_id'] == 0)) {
?>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['password'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" name="password1" size="40" type="password" value="<?php echo $show_password?>" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['verify_password'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" name="password2" type="password" size="40" value="<?php echo $verify_password?>" maxlength="50">
					</td>
				</tr>
<?php
} /* End of native password */
?>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['language'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="language">
<?php
$get_language_sql = "select * from ".$GLOBALS['BR_language_table']." order by language_desc";
$get_language_result = $GLOBALS['connection']->Execute($get_language_sql) or DBError(__FILE__.":".__LINE__);
while ($lang_row = $get_language_result->FetchRow()) {
	$this_language = $lang_row["language"];
	$this_language_desc = $lang_row["language_desc"];
	if ($this_language == $language) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
							<option value="'.$this_language.'" '.$selected.'>'.$this_language_desc.'</option>';
}
?>

						</select>
					</td>
				</tr>
<?php
// �p�G�O���v�ק�@��ϥΪ̡A�h��ܿ��ϥΪ̥i�HŪ��{�������
if (($_GET['user_id'] != 0) && ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
?>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['account_status'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="enable_login">
<?php
	if ($account_disabled == 't') {
		echo '
							<option value="1">'.$STRING['account_enabled'].'</option>
							<option value="0" selected>'.$STRING['account_disabled'].'</option>';
	}else{
		echo '
							<option value="1" selected>'.$STRING['account_enabled'].'</option>
							<option value="0">'.$STRING['account_disabled'].'</option>';
	}
?>

						</select>
					</td>
				</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo $STRING['project_visible']?></legend>
			
				<table class="table-input-form" align="center">
				<tr>
					<td width="100%">
<?php
	if (sizeof($project_array) > 0) {
		echo '<input type="checkbox" name="checkall" onclick="check_access_all();" class=checkbox><b>'.$STRING['select_all'].'</b>';
	} else {
		echo "&nbsp;";
	}
      
?>
					</td>
				</tr>
<?php

	// ��ܩҦ����Q�װϥH�ѿ��
	for ($i=0; $i < sizeof($project_array); $i++) {
		$project_id = $project_array[$i]->getprojectid();
		$project_name = $project_array[$i]->getprojectname();

		$check_list = "select * from ".$GLOBALS['BR_proj_access_table']." where user_id=".$GLOBALS['connection']->QMagic($_GET['user_id'])." and 
			project_id=".$GLOBALS['connection']->QMagic($project_id);
		$check_result = $GLOBALS['connection']->Execute($check_list) or DBError(__FILE__.":".__LINE__);
		$check_line = $check_result->Recordcount();
		if ($check_line == 1) {
			$ischecked="checked";
		}else{
			$ischecked="";
		}
		echo '
				<tr>
					<td>
						<input type="checkbox" name="project'.$i.'" value="'.$project_id.'" class="checkbox" '.$ischecked.'>
						'.htmlspecialchars($project_name).'
					</td>
				</tr>';
	} // end of while
} /* end of show project access setting */
?>		

				</table>
			</fieldset>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
include("../include/tail.php");
?>
