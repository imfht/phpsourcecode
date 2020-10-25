<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: area_show.php,v 1.8 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/project_function.php");

AuthCheckAndLogin();

// ���ˬd�O�_���U�C���
if (!isset($_GET['project_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
$project_sql = "select * from ".$GLOBALS['BR_project_table']."  where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);;
$line = $project_result->Recordcount();
if ($line != 1) {
	WriteSyslog("warn", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$project_name = $project_result->fields["project_name"];

$area_sql = "select * from ".$GLOBALS['BR_proj_area_table']." where area_parent=0 and project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." order by area_name";
$area_result = $GLOBALS['connection']->Execute($area_sql) or DBError(__FILE__.":".__LINE__);
?>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<?php echo htmlspecialchars($project_name)." ".$STRING['task_force']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['task_force']?></tt>
			</td>
			<td nowrap valign="bottom">
				<a href="../index.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">
			<tr>
				<td width="2%" class="title">&nbsp;</td>
				<td width="32%" class="title" align="center"><?php echo $STRING['area']?></td>
				<td width="32%" class="title" align="center"><?php echo $STRING['minor_area']?></td>
				<td width="32%" class="title" align="center"><?php echo $STRING['area_owner']?></td>
				<td width="2%" class="title">&nbsp;</td>
			</tr>
<?php
$userarray = GetAllUsers(0, 1);
while ($row = $area_result->FetchRow()) {
	$area_id = $row["area_id"];
	$area_name = $row["area_name"];
	$area_owner = $row["owner"];

	echo '
			<tr>
				<td class="line1">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="line1">'.$area_name.'</td>
				<td class="line1">&nbsp;</td>
				<td class="line1">';

	echo UidToUsername($userarray, $area_owner);
	
	echo '
				</td>
				<td class="line1">&nbsp;</td>
			</tr>';
	
	$minor_area_sql = "select * from ".$GLOBALS['BR_proj_area_table']." where
					area_parent=$area_id order by area_name";
	$minor_area_result = $GLOBALS['connection']->Execute($minor_area_sql) or
			DBError(__FILE__.":".__LINE__);

	while ($minor_row = $minor_area_result->FetchRow()) {
		$minor_area_name = $minor_row["area_name"];
		$minor_area_owner = $minor_row["owner"];
		
		echo '
			<tr>
				<td class="line0">&nbsp;</td><td class="line0">&nbsp;</td>
				<td class="line0">'.$minor_area_name.'</td>
				<td class="line0">';
		echo UidToUsername($userarray, $minor_area_owner);
		echo '
				</td>
				<td width="10" class="line0">&nbsp;</td>
			</tr>';
	}
}
?>
		</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
include("../include/tail.php");
?>
