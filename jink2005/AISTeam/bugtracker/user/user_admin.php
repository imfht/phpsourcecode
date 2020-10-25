<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: user_admin.php,v 1.18 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_user'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!$_GET['sort_by']) {
	$sort_by = "user_id";
} else {
	if (false === strpos($_GET['sort_by'], ';') && false === strpos($_GET['sort_by'], ' ')) {
		$sort_by = $_GET['sort_by'];
	}
}
if (!$_GET['sort_method']) {
	$sort_method = "ASC";
} else {
	$sort_method = $_GET['sort_method'];
}
if ($sort_method != "DESC") {
	$sort_method = "ASC";
}
if ($sort_method == "ASC") {
	$new_sort_method = "DESC";
} else {
	$new_sort_method = "ASC";
}
$group_array = GetAllGroups();

if ($_GET['user_type'] == "all") {
	// No more condition
} elseif ($_GET['user_type'] == "disabled") {
	$condition = " where account_disabled='t'";
} else {
	$_GET['user_type'] = "valid";
	$condition = " where account_disabled!='t'";
}

?>

<script language="JavaScript" type="text/javascript">
<!--
function ReloadPage()
{
	document.type_form.submit();
}

function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['user'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'user_delete.php?user_id='+id+'&user_type=<?php echo $_GET['user_type']?>';
				}
				return;
			}
	});
}
//-->
</script>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<?php echo $STRING['user_management']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_user.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['user_management']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
				<form method="get" action="<?php echo $_SERVER['PHP_SELF']?>" name="type_form">
				<select name="user_type" onChange="ReloadPage();">
<?php

$all_type = array("valid", "disabled", "all");
$all_type_string = array($STRING['show_valid'], $STRING['show_disabled'], $STRING['show_all']);
for ($i=0; $i<sizeof($all_type); $i++) {
	if ($all_type[$i] == $_GET['user_type']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
					<option value="'.$all_type[$i].'" '.$selected.'>'.$all_type_string[$i].'</option>';
}
?>
				</select>
				</form>
			</td>
			<td nowrap valign="bottom">
				<a href="user_new.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_user.png" border="0" align="middle"><?php echo $STRING['new_user']?></a>
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
				<td width="10" class="title">&nbsp;</td>
				<td width="50" class="title" align="center">
					<a href="user_admin.php?user_type=<?php echo $_GET['user_type']?>&sort_by=user_id&sort_method=<?php echo $new_sort_method?>">
						<?php echo $STRING['id']?>
					</a>
				</td>
				<td width="140" class="title" align="center">
					<a href="user_admin.php?user_type=<?php echo $_GET['user_type']?>&sort_by=username&sort_method=<?php echo $new_sort_method?>">
						<?php echo $STRING['username']?>
					</a>
				</td>
				<td width="110" class="title" align="center">
					<a href="user_admin.php?user_type=<?php echo $_GET['user_type']?>&sort_by=group_name&sort_method=<?php echo $new_sort_method?>">
						<?php echo $STRING['group_name']?>
					</a>
				</td>
				<td width="150" class="title" align="center">
					<?php echo $STRING['real_name']?>
				</td>
				<td width="130" class="title" align="center">
					<?php echo $STRING['created_date']?>
				</td>
				<td width="100" class="title" align="center">
					<?php echo $STRING['function']?>
				</td>
				<td width="10" class="title">&nbsp;</td>
			</tr>
<?php

$all_user_sql="SELECT * FROM ".$GLOBALS['BR_user_table'].
		" LEFT JOIN ".$GLOBALS['BR_group_table']." ON ".
		$GLOBALS['BR_group_table'].".group_id=".$GLOBALS['BR_user_table'].".group_id $condition order by $sort_by $sort_method";

$all_user_result = $GLOBALS['connection']->Execute($all_user_sql) or DBError(__FILE__.":".__LINE__);

$count=0;
while ($all_user_row = $all_user_result->FetchRow()) {
	$user_id = $all_user_row["user_id"];
	$username = $all_user_row["username"];
	$group_id = $all_user_row["group_id"];
	$group_name = $all_user_row["group_name"];
	$realname = $all_user_row["realname"];
	$created_date = $all_user_result->UserTimeStamp($all_user_row["created_date"], GetDateFormat());
	$account_disabled = $all_user_row["account_disabled"];
	$td_class = "line".($count%2);

	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="'.$td_class.'" align="center">'.$user_id.'</td>
				<td class="'.$td_class.'" align="center">';
	if ($account_disabled == 't') {
		echo "<font color=gray>$username (disabled)</font>";
	} else {
		echo "$username";
	}
	echo '
				</td>
				<td class="'.$td_class.'" align="center">'.htmlspecialchars($group_name).'</td>
				<td class="'.$td_class.'" align="center">'.$realname.'</td>
				<td class="'.$td_class.'" align="center">'.$created_date.'</td>
				<td class="'.$td_class.'" align="center">';
	if (($user_id != 0) || ($_SESSION[SESSION_PREFIX.'uid'] == 0)) {
		echo '
					<a href="user_edit.php?user_id='.$user_id.'&user_type='.$_GET['user_type'].'">'.$STRING['edit'].'</a>&nbsp;&nbsp;';
	}
	if ($user_id != 0) {
		echo '
					<a href="JavaScript:ConfirmDelete('.$user_id.');">'.$STRING['delete'].'</a>';
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
