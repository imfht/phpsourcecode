<?php
/* Copyright(c) 2003-2007 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_user_admin.php,v 1.15 2013/06/29 08:30:59 alex Exp $
 *
 */
include("../include/header.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['customer_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_id");
}

if (!$_GET['sort_by']) {
	$sort_by = "customer_user_id";
} else {
	// Avoid SQL injection
	if (false === strpos($_GET['sort_by'], ';') && false === strpos($_GET['sort_by'], ' ')) {
		$sort_by = $_GET['sort_by'];
	}
}

if (!$_GET['sort_method']) {
	$sort_method = "ASC";
} else {
	$sort_method = $_GET['sort_method'];
}

if ($sort_method == "ASC") {
	$new_sort_method = "DESC";
} else {
	$new_sort_method = "ASC";

	// Set the $sort_method to avoid sql injection on $_GET['sort_method'];
	$sort_method = "DESC";
}

$sql = "select * from ".$GLOBALS['BR_customer_table']." where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer");
}
$customer_name = $result->fields["customer_name"];

$count_sql = "select count(*) from ".$GLOBALS['BR_customer_user_table']." 
					where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

$perpage_sql = "select perpage from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$perpage_result = $GLOBALS['connection']->Execute($perpage_sql) or DBError(__FILE__.":".__LINE__);
if ($perpage_result->Recordcount() == 0) {
	ErrorPrintOut("no_such_xxx", "user");
}
$perpage = $perpage_result->fields["perpage"];

if (!isset($_GET['page'])) {
	$page = 1;
} else {
	$page = $_GET['page'];
}
$startat = ($page-1) * $perpage;

?>
<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['customer_user'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'customer_user_delete.php?customer_user_id='+id;
				}
				return;
			}
	});

	return;
}
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="customer_admin.php"><?php echo $STRING['customer_management']?></a> / <?php echo $customer_name?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
			<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_customer_user.png" width="48" height="48" align="middle" border="0">
			<tt class="outline"><?php echo $customer_name?></tt></td>
			<td nowrap width="100%" align="center" valign="bottom">
			<td nowrap valign="bottom">
				<a href="customer_user_new.php?customer_id=<?php echo $_GET['customer_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_customer_user.png" border="0" align="middle"><?php echo $STRING['new_customer_user']?></a>
			</td>
			<td nowrap valign="bottom">
				<a href="customer_admin.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
								  
			<table class="table-main-list" align="center">
			<tr>
				<td width="100%" colspan="6" align="center">
				
<?php
PrintPageLink($count, $page, $perpage, $_SERVER['PHP_SELF'], 
			  "customer_id=".$_GET['customer_id']."&sort_by=$sort_by&sort_method=$sort_method");
?>
				</td>
			</tr>
			<tr>
				<td width="10" class="title">&nbsp;</td>
				<td width="160" class="title" align="center">
					<a href="<?php echo $_SERVER['PHP_SELF']?>?page=<?php echo $page?>&customer_id=<?php echo $_GET['customer_id']?>&sort_by=realname&sort_method=<?php echo $new_sort_method?>">
					<?php echo $STRING['real_name']?></a>
				</td>
				<td width="240" class="title" align="center">
					<a href="<?php echo $_SERVER['PHP_SELF']?>?page=<?php echo $page?>&customer_id=<?php echo $_GET['customer_id']?>&sort_by=email&sort_method=<?php echo $new_sort_method?>">
					<?php echo $STRING['email']?></a>
				</td>
				<td width="130" class="title" align="center">
					<a href="<?php echo $_SERVER['PHP_SELF']?>?page=<?php echo $page?>&customer_id=<?php echo $_GET['customer_id']?>&sort_by=created_date&sort_method=<?php echo $new_sort_method?>">
					<?php echo $STRING['created_date']?></a>
				</td>
				<td width="150" class="title" align="center"><?php echo $STRING['function']?></td>
				<td width="10" valign="top" class="title">&nbsp;</td>
			</tr>
<?php

$all_customer_sql = "select * from ".$GLOBALS['BR_customer_user_table']." 
					where customer_id='".$_GET['customer_id']."' 
					order by ".$sort_by." $sort_method";

$all_customer_result = $GLOBALS['connection']->SelectLimit($all_customer_sql, $perpage, $startat) or 
			DBError(__FILE__.":".__LINE__);

$style_count = 0;
while ($row = $all_customer_result->FetchRow()) {
	$id = $row["customer_user_id"];
	$realname = $row["realname"];
	$email = $row["email"];
	$account_disabled = $row["account_disabled"];
	$created_date = $all_customer_result->UserTimeStamp($row["created_date"], GetDateFormat());
	$td_class = "line".($style_count%2);
  
	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="'.$td_class.'">';
	if ($account_disabled == 't') {
		echo '<font color=gray>'.$realname.'</font>';
	} else {
		echo $realname;
	}
	echo '
				</td>
				<td class="'.$td_class.'">';
	if ($account_disabled == 't') {
		echo '<font color=gray>'.$email.' (disabled)</font>';
	} else {
		echo '<a href="mailto:'.$email.'">'.$email.'</a>';
	}
	echo '
				</td>
				<td class="'.$td_class.'" align="center">'.$created_date.'</td>
				<td class="'.$td_class.'" height="30" align="center">
					<a href="customer_user_edit.php?customer_user_id='.$id.'">'.$STRING['edit'].'</a>&nbsp;&nbsp;
					<a href="JavaScript:ConfirmDelete('.$id.');">'.$STRING['delete'].'</a>
				</td>
				<td class="'.$td_class.'">&nbsp;</td>
			</tr>';
	$style_count++;
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
