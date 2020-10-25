<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_admin.php,v 1.11 2008/11/30 03:46:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

?>
<script language="JavaScript" type="text/javascript">
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['customer'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'customer_delete.php?customer_id='+id;
				}
				return;
			}
	});
}
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> / 
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> / <?php echo $STRING['customer_management']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_customer.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['customer_management']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
			<td nowrap valign="bottom">
				<a href="customer_new.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_customer.png" border="0" align="middle"><?php echo $STRING['new_customer']?></a>
			</td>
			<td nowrap valign="bottom">
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
				<td width="40" class="title" align="center"><p><?php echo $STRING['id']?></p></td>
				<td width="270" class="title" align="center"><?php echo $STRING['customer_name']?></td>
				<td width="120" class="title" align="center"><?php echo $STRING['customer_user']?></td>
				<td width="110" class="title" align="center"><?php echo $STRING['created_date']?></td>
				<td width="160" class="title" align="center"><?php echo $STRING['function']?></td>
				<td width="10" valign="top" class="title">&nbsp;</td>
			</tr>
<?php
	
$all_customer_sql = "select * from ".$GLOBALS['BR_customer_table']." order by customer_id";

$all_customer_result = $GLOBALS['connection']->Execute($all_customer_sql) or 
	DBError(__FILE__.":".__LINE__);

$count = 0;
while ($row = $all_customer_result->FetchRow()) {
	$customer_id = $row["customer_id"];
	$customer_name = $row["customer_name"];
	$created_date = $all_customer_result->UserTimeStamp($row["created_date"], GetDateFormat());
	$td_class = "line".($count%2);

	$count_user_sql = "select count(*) from ".$GLOBALS['BR_customer_user_table']." where customer_id=$customer_id";
	$count_user_result = $GLOBALS['connection']->Execute($count_user_sql) or DBError(__FILE__.":".__LINE__);
	$user_count = $count_user_result->fields[0];
  
	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="'.$td_class.'" align="center">'.$customer_id.'</td>
				<td class="'.$td_class.'">'.$customer_name.'</td>
				<td class="'.$td_class.'" align="center">'.$user_count.'</td>
				<td class="'.$td_class.'" align="center">'.$created_date.'</td>
				<td class="'.$td_class.'" height="30" align="center">
					<a href="customer_user_admin.php?customer_id='.$customer_id.'">'.$STRING['user'].'</a>&nbsp;&nbsp;
					<a href="customer_edit.php?customer_id='.$customer_id.'">'.$STRING['edit'].'</a>&nbsp;&nbsp;';
	if ($customer_id != 0) {
		echo '
					<a href="JavaScript:ConfirmDelete('.$customer_id.');">'.$STRING['delete'].'</a>';
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
