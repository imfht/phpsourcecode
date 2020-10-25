<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_subscribe.php,v 1.7 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

$project_sql = "select project_name from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$line = $project_result->Recordcount();
if ($line != 1) {
       ErrorPrintOut("no_such_xxx", "project");
}
$project_name = $project_result->fields['project_name'];

$sql = "select ".$GLOBALS['BR_user_table'].".username, ".$GLOBALS['BR_user_table'].".user_id 
		from ".$GLOBALS['BR_user_table'].", ".$GLOBALS['BR_proj_auto_mailto_table']." where ".
		$GLOBALS['BR_user_table'].".user_id=".$GLOBALS['BR_proj_auto_mailto_table'].
		".user_id and project_id = ".$GLOBALS['connection']->QMagic($_GET['project_id'])." and ".
		$GLOBALS['BR_proj_auto_mailto_table'].".can_unsubscribe='t' order by ".
		$GLOBALS['BR_user_table'].".username";
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
?>
<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['subscribe'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'project_dosubscribe.php?project_id=<?php echo $_GET['project_id']?>&user_id='+id+'&subscribe=n';
				}
				return;
			}
	});
}
</script>
<a href="project_dosubscribe.php?project_id='.$_GET['project_id'].'&user_id='.$user_id.'&subscribe=n">

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<?php echo htmlspecialchars($project_name)?> <?php echo $STRING['subscribe_list']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['subscribe_list']?></tt>
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
				<td class="title" width="10">&nbsp;</td>
				<td class="title" width="300" align="left"><?php echo $STRING['username']?></td>
				<td class="title" width="180" align="center"><?php echo $STRING['function']?></td>
				<td valign="top" class="title" width="10">&nbsp;</td>
			</tr>
	
<?php
$count=0;
while ($row = $result->FetchRow()){
	$username = $row['username'];
	$user_id = $row['user_id'];

	echo '
			<tr>
				<td valign="top" class="line'.($count%2).'">&nbsp;</td>
				<td valign="top" class="line'.($count%2).'">'.$username.'</td>
				<td valign="top" class="line'.($count%2).'" align="center">
					<a href="JavaScript:ConfirmDelete('.$user_id.');">
						'.$STRING['delete'].'
					</a>
				</td>
				<td valign="top" class="line'.($count%2).'">&nbsp;</td>
			</tr>';
} // end of while

?>

			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
include("../include/tail.php");
?>
