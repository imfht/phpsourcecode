<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: filter_setting.php,v 1.13 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();
?>

<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['filter'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'filter_drop.php?filter_id='+id+'<?php echo ($_GET['project_id'] == "")?'':'&project_id='.$_GET['project_id']?>';
				}
				return;
			}
	});
}
</script>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['set_filter']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_filter.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['set_filter']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="filter_new.php<?php if ($_GET['project_id'] != "") {echo '?project_id='.$_GET['project_id'];}?>">
					<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_filter.png" border="0" align="middle"><?php echo $STRING['new_filter']?>
				</a>
<?php
if ($_GET['project_id'] != "") {
	echo '
				<a href="../report/project_list.php?project_id='.$_GET['project_id'].'">';
} else {
	echo '
				<a href="../system/system.php">';
}
?>
                <img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?>
				</a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">
			<tr>
				<td width="340" align="center" class="title"><?php echo $STRING['filter_name']?></td>
				<td width="160" align="center" class="title"><?php echo $STRING['function']?></td>
			</tr>
<?php
$filter_sql = "select * from ".$GLOBALS['BR_filter_table']." where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid'])." order by filter_name";
$filter_result = $GLOBALS['connection']->Execute($filter_sql) or DBError(__FILE__.":".__LINE__);
$line = $filter_result->Recordcount();
// �p�G�쥻�N����ơA�h��ܲ{�������
if ($line!=0) {
	$counter=0;
	while ($row = $filter_result->FetchRow()){
		$filter_id = $row["filter_id"];
		$filter_name = $row["filter_name"];
		$td_class = "line".($counter%2);

		echo '
			<tr>
				<td class="'.$td_class.'">
					<a href="filter_show.php?filter_id='.$filter_id.'">'.$filter_name.'</a>
				</td>
				<td align="center" class="'.$td_class.'">
					<a href="JavaScript:ConfirmDelete('.$filter_id.')">'.$STRING['delete'].'</a>
				</td>
			</tr>';
		$counter++;
	}
} else {
	echo '
			<tr>
				<td align="center">
					<font color="red">'.$STRING['no_filter_now'].'</font>
				</td>
			</tr>';
}
?>

			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>>
<?php

include("../include/tail.php");
?>
