<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_new.php,v 1.15 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (isset($_SESSION[SESSION_PREFIX.'back_array']['user_type'])) {
	$_GET['user_type'] = $_SESSION[SESSION_PREFIX.'back_array']['user_type'];
}
$project_array = GetAllProjects();
	
?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.newuser;
	var y='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>';
	if(!f.username.value){y+='-<?php echo addslashes($STRING['username'])?><br>';}
	if(f.group_id.options[f.group_id.selectedIndex].value==-1){y+='-<?php echo addslashes($STRING['group_name'])?><br>';}
	if(!f.email.value){y+='-<?php echo addslashes($STRING['email'])?><br>';}
<?php
if ($SYSTEM['auth_method'] == "native") {
?>
	if(f.password1.value!=f.password2.value) {y+='<br><?php echo addslashes($STRING['password_not_match'])?>';}
<?php
}
?>
	if(y=='<?php echo addslashes($STRING['input_the_follow_info']).$STRING['colon']?><br>'){
		return OnSubmit(f);
	}else {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_user'])?>',
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
	if (document.newuser.checkall.checked) {
		checkall = true;
	} else {
		checkall = false;
	}
<?php
	for ($i=0; $i<sizeof($project_array); $i++) {
		echo "if (document.newuser.project".$i.") { \n";
		echo "	document.newuser.project".$i.".checked=checkall;";
		echo "} \n";
	}
?>
	return true;
}
function oncancel()
{
	parent.location='user_admin.php';
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="user_admin.php"><?php echo $STRING['user_management']?></a> /
	<?php echo $STRING['new_user']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_user.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['new_user']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="user_admin.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<form method="POST" action="user_donew.php" onsubmit="return check1();" name="newuser">
			<input type="hidden" name="user_type" value="<?php echo $_GET['user_type']?>">

			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>
			
				<table class="table-input-form" align="center">
				<tr>
					<td width="33%" class="item_prompt_small">

<?php
echo $STRING['username'].$STRING['colon'];

$hint = str_replace("@string@", $reserve_words, $STRING['reserve_hint']);
$hint = str_replace("@key@", $STRING['username'], $hint);
$hint = htmlspecialchars($hint);
PrintTip($STRING['hint_title'], $hint);
?>
					</td>
					<td width="67%">
						<input class="input-form-text-field" type="text" name="username" size="40" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['username']?>" maxlength="20">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['group_name'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="group_id">
							<option value="-1"></optopn>
<?php

$group_sql = "select * from ".$GLOBALS['BR_group_table']." order by group_name";
$group_result = $GLOBALS['connection']->Execute($group_sql) or DBError(__FILE__.":".__LINE__);
while ($all_group_row = $group_result->FetchRow()) {
	$the_group_id = $all_group_row["group_id"];
	$the_group_name = htmlspecialchars($all_group_row["group_name"]);
	if (isset($_SESSION[SESSION_PREFIX.'back_array']['group_id']) && ($_SESSION[SESSION_PREFIX.'back_array']['group_id'] == $the_group_id)) {
		echo '
							<option value="'.$the_group_id.'" selected>'.$the_group_name.'</option>';
	} else {
		echo '
							<option value="'.$the_group_id.'">'.$the_group_name.'</option>';
	}
	
}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['real_name'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" type="text" name="realname" size="40" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['realname']?>" maxlength="100">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['email'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" type="text" name="email" size="40" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['email']?>" maxlength="60">
					</td>
				</tr>
<?php
if ($SYSTEM['auth_method'] == "native") {
?>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['password'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" name="password1" size="40" type="password" maxlength="50">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['verify_password'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" name="password2" type="password" size="40" maxlength="50">
					</td>
				</tr>
<?php
}
?>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['language'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="language">
<?php
$get_lang_sql = "select language from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$get_lang_result = $GLOBALS['connection']->Execute($get_lang_sql) or DBError(__FILE__.":".__LINE__);
if ($get_lang_result->RecordCount() == 0) {
	$def_language = GetDefaultLang();
} else {
	$def_language = $get_lang_result->fields["language"];
}
if ($def_language == "") {
	$def_language = "en";
}

$get_language_sql = "select * from ".$GLOBALS['BR_language_table']." order by language_desc";
$get_language_result = $GLOBALS['connection']->Execute($get_language_sql) or DBError(__FILE__.":".__LINE__);
while ($lang_row = $get_language_result->FetchRow()) {
	$language = $lang_row["language"];
	$language_desc = $lang_row["language_desc"];
	if (isset($_SESSION[SESSION_PREFIX.'back_array'])) {
		if ($_SESSION[SESSION_PREFIX.'back_array']['language'] == $language) {
			$selected = "selected";
		} else {
			$selected = "";
		}
	} else {
		if ($language == $def_language) {
			$selected = "selected";
		} else {
			$selected = "";
		}
	}
	
	echo '
							<option value="'.$language.'" '.$selected.'>'.$language_desc.'</option>';
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
echo '
					</td>
				</tr>';
// ��ܩҦ��� project �H�ѿ��
for ($i=0; $i<sizeof($project_array); $i++) {
	$project_id = $project_array[$i]->getprojectid();
	$project_name = $project_array[$i]->getprojectname();
	echo '
				<tr>
					<td>';
	if (isset($_SESSION[SESSION_PREFIX.'back_array']['project'.$i]) && ($_SESSION[SESSION_PREFIX.'back_array']['project'.$i] == $project_id)) {
		echo '
						<input type="checkbox" name="project'.$i.'" value="'.$project_id.'" class="checkbox" checked>';
	} else {
		echo '
						<input type="checkbox" name="project'.$i.'" value="'.$project_id.'" class="checkbox">';
	}

	echo '
						'.htmlspecialchars($project_name).'
					</td>
				</tr>';
}

?>
				</table>
			</fieldset>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" name="B1" class="button">
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
include("../include/tail.php");
?>
