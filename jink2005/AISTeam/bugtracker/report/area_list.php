<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: area_list.php,v 1.12 2013/06/30 21:45:28 alex Exp $
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

// ���ˬd�O�_���U�C���
if (!isset($_GET['project_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$area_sql = "select * from ".$GLOBALS['BR_proj_area_table']." where area_parent=0 and project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." order by area_name";
$area_result = $GLOBALS['connection']->Execute($area_sql) or DBError(__FILE__.":".__LINE__);
$count_area = $area_result->Recordcount();
?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_edit.php?project_id=<?php echo $_GET['project_id']?>"><?php echo $STRING['edit_project']?></a> /
	<?php echo $STRING['area_minor_area']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['area_minor_area']?></tt>
			</td>
			<td nowrap valign="bottom">
				<a href="project_edit.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			
			<form method="POST" action="area_dolist.php" OnSubmit="return OnSubmit(this);">
			<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
		
			<table class="table-main-list" align="center">
			<tr>
				<td width="2%" class="title">&nbsp;</td>
				<td width="32%" class="title" align="center"><?php echo $STRING['area']?></td>
				<td width="32%" class="title" align="center"><?php echo $STRING['minor_area']?></td>
				<td width="32%" class="title" align="center"><?php echo $STRING['area_owner']?></td>
				<td width="2%" class="title">&nbsp;</td>
			</tr>
<?php
$all_userarray = GetAllUsers(0, 1);
$userarray = array();
for ($i = 0; $i < sizeof($all_userarray); $i++) {
	$user_id = $all_userarray[$i]->getuserid();
	if (CheckProjectAccessable($_GET['project_id'], $user_id) == FALSE) {
		continue;
	} else {
		array_push($userarray, $all_userarray[$i]);
	}
}

for ($i=0; $i < $SYSTEM['max_area']; $i++) {
	if ($i < $count_area) {
		$area_row = $area_result->FetchRow();
		$area_id = $area_row["area_id"];
		$area_name = $area_row["area_name"];
		$area_owner = $area_row["owner"];
	} else {
		$area_name = "";
		$area_owner = -1;
	}

	echo '
			<tr>
				<td class="line1">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="line1" align="center">
					<input class="input-form-text-field" type="text" name="area'.$i.'" value="'.$area_name.'" size="20" maxlength="40">
				</td>
				<td class="line1">&nbsp;</td>
				<td class="line1" align="center">
					<select size="1" name="area_owner'.$i.'">
						<option value="-1"></option>';

	for ($j = 0; $j < sizeof($userarray); $j++) {
		
		$area_user_id = $userarray[$j]->getuserid();
		if ($area_owner == $area_user_id) {
			$selected = "selected";
		} else {
			if ($userarray[$j]->getdisabled()) {
				
				continue;
			}
			$selected = "";
		}
		
		echo '
						<option value="'.$area_user_id.'" '.$selected.'>';
		
		echo $userarray[$j]->getusername();
		if ($userarray[$j]->getdisabled()) {
			echo "(disabled)";
		}
		echo '</option>';
	}
	echo '
					</select>
				</td>
				<td class="line1">&nbsp;</td>
			</tr>';

	if ($i < $count_area) {
		$minor_area_sql = "select * from ".$GLOBALS['BR_proj_area_table']." where
					area_parent=$area_id order by area_name";
		$minor_area_result = $GLOBALS['connection']->Execute($minor_area_sql) or
				DBError(__FILE__.":".__LINE__);
		$count_minor_area = $minor_area_result->Recordcount();
	} else {
		$count_minor_area = 0;
	}
	for ($j = 0; $j < $SYSTEM['max_minor_area']; $j++) {
		if ($j < $count_minor_area) {
			$minor_row = $minor_area_result->FetchRow();
			$minor_area_name = $minor_row["area_name"];
			$minor_area_owner = $minor_row["owner"];
		} else {
			$minor_area_name = "";
			$minor_area_owner = -1;
		}
		echo '
			<tr>
				<td class="line0">&nbsp;</td>
				<td class="line0">&nbsp;</td>
				<td align="center" class="line0">
					<input class="input-form-text-field" type="text" name="minor_area'.$i.'_'.$j.'" value="'.$minor_area_name.'" size="20" maxlength="40"></td>
				<td align="center" class="line0">
					<select size="1" name="minor_area_owner'.$i.'_'.$j.'">
						<option value="-1"></option>';
		for ($k = 0; $k < sizeof($userarray); $k++) {
			$minor_user_id = $userarray[$k]->getuserid();
			if ($minor_area_owner == $minor_user_id) {
				$selected = "selected";
			} else {
				if ($userarray[$k]->getdisabled()) {
					continue;
				}
				$selected = "";
			}
			echo '
						<option value="'.$minor_user_id.'" '.$selected.'>';
			echo $userarray[$k]->getusername();
			if ($userarray[$k]->getdisabled()) {
				echo "(disabled)";
			}
			echo '</option>';
		}
		echo '
					</select>
				</td>
				<td width="10" class="line0">&nbsp;</td>
			</tr>';
	}
}
?>
			</table>
			<p align="center"><input type="submit" name="ok" value="<?php echo $STRING['button_submit']?>" class="button"></p>
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
include("../include/tail.php");
?>
