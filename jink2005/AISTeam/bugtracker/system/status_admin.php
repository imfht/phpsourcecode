<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: status_admin.php,v 1.12 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_status'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

?>

<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['status'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'status_delete.php?status_id='+id;
				}
				return;
			}
	});
}
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['status_management']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_status.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['status_management']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="status_new.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_status.png" border="0" align="middle"><?php echo $STRING['new_status']?></a>
				<a href="system.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
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
				<td align="center" width="80" class="title"><?php echo $STRING['id']?></td>
				<td align="center" width="230" class="title"><?php echo $STRING['status_name']?></td>
				<td align="center" width="110" class="title"><?php echo $STRING['color']?></td>
				<td align="center" width="110" class="title"><?php echo $STRING['type']?></td>
				<td align="center" width="110" class="title"><?php echo $STRING['function']?></td>
				<td class="title" width="10">&nbsp;</td>
			</tr>
<?php
$all_status_sql = "select * from ".$GLOBALS['BR_status_table']." order by status_name ASC";
$all_status_result = $GLOBALS['connection']->Execute($all_status_sql) or DBError(__FILE__.":".__LINE__);;
$count=0;
while ($row = $all_status_result->FetchRow()) {
	$status_id = $row["status_id"];
	$status_name = $row["status_name"];
	$status_color = $row["status_color"];
	$status_type = ($row["status_type"] == "active")? $STRING['status_type_active']:$STRING['status_type_closed'];
	$td_class="line".($count%2);
  
	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td align="center" class="'.$td_class.'">'.$status_id.'</td>
				<td align="left" class="'.$td_class.'">'.htmlspecialchars($status_name).'</td>
				<td align="center" class="'.$td_class.'">
					<font color="'.$status_color.'">'.$status_color.'</font>
				</td>
				<td align="center" class="'.$td_class.'">'.$status_type.'</td>
				<td align="center" class="'.$td_class.'">
					<a href="status_edit.php?status_id='.$status_id.'">'.$STRING['edit'].'</a>&nbsp;&nbsp;
					<a href="JavaScript:ConfirmDelete('.$status_id.')">'.$STRING['delete'].'</a>
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
