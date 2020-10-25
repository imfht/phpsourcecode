<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: group_admin.php,v 1.12 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$all_group_sql = "select * from ".$GLOBALS['BR_group_table']." order by group_id ASC";
$all_group_result = $GLOBALS['connection']->Execute($all_group_sql) or DBError(__FILE__.":".__LINE__);;
?>

<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['group'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'group_delete.php?group_id='+id;
				}
				return;
			}
	});
}
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['group_management']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_group.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['group_management']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="group_new.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_group.png" border="0" align="middle"><?php echo $STRING['new_group']?></a>
				<a href="../system/system.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
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
				<td align="center" width="80" class="title">
					<?php echo $STRING['id']?>
				</td>
				<td align="center" width="300" class="title">
					<?php echo $STRING['group_name']?>
				</td>
				<td align="center" width="100" class="title">
					<?php echo $STRING['function']?>
				</td>
				<td class="title" width="10">&nbsp;</td>
			</tr>
<?php
$count=0;
while ($all_group_row = $all_group_result->FetchRow()) {
	$group_id = $all_group_row["group_id"];
	$group_name = $all_group_row["group_name"];
	$td_class = "line".($count%2);
  
	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td align="center" class="'.$td_class.'">'.$group_id.'</td>
				<td align="left" class="'.$td_class.'">'.htmlspecialchars($group_name).'</td>
				<td align="center" class="'.$td_class.'">';
	if ($group_id != 0) {
		echo '
					<a href="group_edit.php?group_id='.$group_id.'">'.$STRING['edit'].'</a>&nbsp;&nbsp;
					<a href="JavaScript:ConfirmDelete('.$group_id.');">'.$STRING['delete'].'</a>';
	}else{
		echo '
					System';
	}
	echo '
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
