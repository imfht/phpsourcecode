<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: syslog.php,v 1.16 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_sysinfo'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

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

if ($_GET['feedback']) {
	$LogTable = $GLOBALS['BR_feedback_syslog_table'];
	$FeedbackSite = true;
} else {
	$userarray = GetAllUsers(1, 1);
	$LogTable = $GLOBALS['BR_syslog_table'];
}

$count_sql = "SELECT count(syslog_id) FROM ".$LogTable;
$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

$sql = "select * from ".$LogTable." order by time DESC";
$result = $GLOBALS['connection']->SelectLimit($sql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);

if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
?>
<script language="JavaScript" type="text/javascript">
function ConfirmDelete()
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['syslog'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					<?php
					if ($_GET['feedback']) {
						echo "location.href= 'syslog_clear.php?feedback=1'";
					} else {
						echo "location.href= 'syslog_clear.php'";
					}
					?>
				}
				return;
			}
	});
}
</script>
<?php
}
?>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php?page=information"><?php echo $STRING['title_information']?></a> /
	<?php echo $_GET['feedback']?$STRING['feedback_syslog']:$STRING['syslog']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_syslog.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $_GET['feedback']?$STRING['feedback_syslog']:$STRING['syslog']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
<?php
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		echo '
				<a href="JavaScript:ConfirmDelete();">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/syslog_clear.png" border="0" align="middle">
					'.$STRING['clear_syslog'].'
				</a>';
	}
	
	echo '<a href="'.$_SERVER['PHP_SELF'].($FeedbackSite?"?feedback=1":"").'">';
	echo '<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/refresh.png" border="0" align="middle">'.$STRING['refresh'].'</a>';
?>
				
				<a href="system.php?page=information"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">
			<tr>
				<td colspan="5" align="center">
<?php
PrintPageLink($count, $page, $perpage, $_SERVER['PHP_SELF'], ($FeedbackSite?"feedback=1":""));
?>

				</td>
			</tr>
			<tr>
				<td align="center" width="50" class="title"><?php echo $STRING['type']?></td>
				<td align="center" width="120" class="title"><?php echo $STRING['username']?></td>
				<td align="center" width="100" class="title"><?php echo $STRING['ip']?></td>
				<td align="center" width="120" class="title"><?php echo $STRING['time']?></td>
				<td class="title" width="370" align="center"><?php echo $STRING['syslog']?></td>
			</tr>
<?php

	$count = 0;
	while ($row = $result->FetchRow()) {
		if ($_GET['feedback']) {
			$user = $row['customer_email'];
			if ($user == "") {
				$user = "admin";
			}
		} else {
			$user_id = $row["user_id"];
			$user = UidToUsername($userarray, $user_id);
		}
		
		$log_type = $row["log_type"];
		$ip = $row["ip"];
		$time = $result->UserTimeStamp($row["time"], GetDateTimeFormat());
		$log_string_key = $row["log_string_key"];
		$arg_key = $row["arg_key"];
		$arg_string = $row["arg_string"];
		$td_class = "line".($count%2);

		$message = $STRING[$log_string_key];

		if ($arg_key != "") {
			$message = str_replace("@key@", $STRING[$arg_key], $message);
		}
		$message = str_replace("@string@", $arg_string, $message);

		echo '
			<tr>
				<td class="'.$td_class.'" align="center">
					<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/log_'.$log_type_array[$log_type].'.gif">
				</td>
				<td align="center" class="'.$td_class.'">'.$user.'</td>
				<td align="center" class="'.$td_class.'">'.$ip.'</td>
				<td align="center" class="'.$td_class.'">'.$time.'</td>
				<td class="'.$td_class.'">
					'.$message.'
				</td>
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
PrintGotoTop();
include("../include/tail.php");
?>
