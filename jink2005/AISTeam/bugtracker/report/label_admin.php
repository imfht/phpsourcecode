<?php
/* Copyright c 2003-2008 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: label_admin.php,v 1.2 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_label'])) {
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
$project_line = $project_result->Recordcount();
if ($project_line == 1) {
	$project_name = $project_result->fields["project_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$sql = "SELECT * FROM ".$GLOBALS['BR_label_table']." WHERE project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." order by label_name ASC";
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);;
?>

<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['label'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'label_delete.php?label_id='+id;
				}
				return;
			}
	});
}
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>"><?php echo $project_name?></a> /
	<?php echo $STRING['label_management']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_label.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['label_management']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="label_new.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_label.png" border="0" align="middle"><?php echo $STRING['new_label']?></a>
				<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">
			<tr>
				<td class="title" width="10">&nbsp;</td>
				<td align="center" width="300" class="title">
					<?php echo $STRING['label']?>
				</td>
				<td align="center" width="200" class="title">
					<?php echo $STRING['color']?>
				</td>
				<td align="center" width="100" class="title">
					<?php echo $STRING['function']?>
				</td>
				<td class="title" width="10">&nbsp;</td>
			</tr>
<?php
$count=0;
while ($row = $result->FetchRow()) {
	$label_id = $row["label_id"];
	$label_name = $row["label_name"];
	$font_color = $label_color_array[$row["color"]][0];
	$bg_color = $label_color_array[$row["color"]][1];
	$td_class = "line".($count%2);

	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td align="left" class="'.$td_class.'">'.$label_name.'</td>
				<td align="center" class="'.$td_class.'">
					<span style="color:'.$font_color.';background-color:'.$bg_color.';">'.$STRING['label'].'</span>
				</td>
				<td align="center" class="'.$td_class.'">
					<a href="label_edit.php?label_id='.$label_id.'">'.$STRING['edit'].'</a>&nbsp;&nbsp;
					<a href="JavaScript:ConfirmDelete('.$label_id.');">'.$STRING['delete'].'</a>
				</td>
				<td class="'.$td_class.'">&nbsp;</td>
			</tr>';
	$count++;
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
