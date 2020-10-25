<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: index.php,v 1.24 2013/07/07 21:29:09 alex Exp $
 *
 */
include("include/header.php");
include("include/user_function.php");
include("include/datetime_function.php");

if ($SYSTEM_VERSION != $SYSTEM['version']) {
?>
	<script language="JavaScript" type="text/javascript">
	location.href= "setup/upgrade.php";
	</script>
<?php
}
AuthCheckAndLogin();

$today=date("md");

if ( ($today > 1215) && ($today < 1231) ) {

?>
<script language="JavaScript" src="javascript/snow.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
SnowShow.Show();
</script>
<?php
}
?>
<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['project'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'report/project_drop.php?project_id='+id;
				}
				return false;
			}
	});
}
</script>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['title_project_list']?></tt>
			</td>
<?php
	if (!isset($_GET['project_id'])) {
		if (($_SESSION[SESSION_PREFIX.'uid'] == 0) || 
			($GLOBALS['Privilege'] & $GLOBALS['can_create_project']) ) {
			echo '
			<td align="left" nowrap>
				<a href="report/project_new.php">
					<img src="images/new_project.png" align="center" width="32" height="32" border="0">
					'.$STRING['new_project'].'
				</a>
			</td>';
		}
	}
?>

		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
		<h3>&nbsp;</h3>
		<table class="table-main-list" align="center">
			<tr>
				<td class="title" width="10">&nbsp;</td>
				<td class="title" width="308" align="left"><?php echo $STRING['project_name']?></td>
				<td class="title" width="120" align="center"><?php echo $STRING['created_date']?></td>
				<td class="title" width="90" align="center"><?php echo $STRING['created_by']?></td>
				<td class="title" width="120" align="center"><?php echo $STRING['function']?></td>
				<td valign="top" width="10" class="title">&nbsp;</td>
			</tr>
	
<?php

	$sql = "select * from ".$GLOBALS['BR_project_table']." order by project_name";
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$user_array = GetAllUsers(1, 1);
	// ��X�i�H��ܪ��Q�װ�
	$count=0;
	while ($row = $sql_result->FetchRow()){
		$project_id = $row['project_id'];
		$visible_sql = "select * from ".$GLOBALS['BR_proj_access_table']." where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid'])." and project_id=".$GLOBALS['connection']->QMagic($project_id);
		$visible_result = $GLOBALS['connection']->Execute($visible_sql) or DBError(__FILE__.":".__LINE__);
		$line = $visible_result->Recordcount();
      
		// �p�G�ӵ{�������\�M�椤���ӨϥΪ̡A�Ψϥά� admin,�h��ܸӵ{�����Q�װ�
		if (($line != 0) || ($_SESSION[SESSION_PREFIX.'uid'] == 0)) {
			$project_name = $row["project_name"];
			$created_date = $sql_result->UserTimeStamp($row["created_date"], GetDateFormat());
			$created_by = $row["created_by"];

			echo '
			<tr>
				<td height="50" class="line'.($count%2).'">
					<img border="0" src="images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="line'.($count%2).'">
					<a href="report/project_list.php?project_id='.$project_id.'"><b>'.htmlspecialchars($project_name).'</b></a><br>';

			if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_feedback']) {
				echo '
					<a href="feedback/feedback_list.php?project_id='.$project_id.'">'.$STRING['title_feedback'].'</a>,&nbsp;';
			}
			if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_faq']) {
				echo '
					<a href="faq/faq_admin.php?project_id='.$project_id.'">'.$STRING['faq'].'</a>,&nbsp;';
			}
			echo '
					<a href="report/area_show.php?project_id='.$project_id.'">'.$STRING['task_force'].'</a>
				</td>
				<td class="line'.($count%2).'" align="center">'.$created_date.'</td>
				<td class="line'.($count%2).'" align="center">'.UidToUsername($user_array, $created_by).'</td>
				<td class="line'.($count%2).'" align="center">';

			if ($GLOBALS['Privilege'] & $GLOBALS['can_update_project']) {
				echo '
					<a href="report/project_edit.php?project_id='.$project_id.'">'.$STRING['edit'].'</a>&nbsp;&nbsp;';
			} else {
				echo $STRING['edit'].'&nbsp;&nbsp;';
			}
			if ($GLOBALS['Privilege'] & $GLOBALS['can_delete_project']) {
				echo '
					<a href="JavaScript:ConfirmDelete('.$project_id.')">'.$STRING['delete'].'</a><br>';
			} else {
				echo $STRING['delete'];
			}
			if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
				echo '
					<a href="report/project_subscribe.php?project_id='.$project_id.'">'.$STRING['subscribe_list'].'</a>';
			} else if ($SYSTEM['allow_subscribe'] == 't') {
				$subscribe_sql = "select can_unsubscribe from ".$GLOBALS['BR_proj_auto_mailto_table'].
					" where project_id=".$GLOBALS['connection']->QMagic($project_id)." and user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
				$subscribe_result = $GLOBALS['connection']->Execute($subscribe_sql) or DBError(__FILE__.":".__LINE__);
				$line = $subscribe_result->Recordcount();
				if ($line == 0) {
					echo '<a href="report/project_dosubscribe.php?project_id='.$project_id.'&subscribe=y">';
					echo $STRING['subscribe'];
					echo '</a>';
				} else if ($subscribe_result->fields["can_unsubscribe"] == 't') {
					echo '
					<a href="report/project_dosubscribe.php?project_id='.$project_id.'&subscribe=n">'.$STRING['unsubscribe'].'</a>';
				}
			}
      
			echo '
				</td>
				<td valign="top" class="line'.($count%2).'">&nbsp;</td>
			</tr>';
			$count++;
		}//end if
    
	}// end of while


?>
			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
include("include/tail.php");
?>
