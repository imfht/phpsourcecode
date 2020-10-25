<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: login_statistic.php,v 1.9 2008/11/28 10:36:31 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_statistic'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_GET['period'] == "latest_week") {
	$timestamp = mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'));
} else if ($_GET['period'] == "latest_year") {
	$timestamp = mktime(0, 0, 0, date('n'), date('j'), date('Y') - 1);
} else if ($_GET['period'] == "latest_half_year") {
	$timestamp = mktime(0, 0, 0, date('n') - 6, date('j'), date('Y'));
} else {
	$timestamp = mktime(0, 0, 0, date('n') - 1, date('j'), date('Y'));
	$_GET['period'] = "latest_month";
}

$the_day = $GLOBALS['connection']->DBTimeStamp($timestamp);

$sql = "select count(login_id) from ".$GLOBALS['BR_login_log_table']." 
		where login_time>=$the_day group by user_id order by count(login_id) DESC";
$result = $GLOBALS['connection']->Execute($sql);
$count_max = $result->fields[0];

$sql = "select count(login_id) as count, username from ".$GLOBALS['BR_login_log_table'].", ".
		$GLOBALS['BR_user_table']." where login_time>$the_day and ".
		$GLOBALS['BR_user_table'].".user_id=".$GLOBALS['BR_login_log_table'].".user_id
		group by ".$GLOBALS['BR_user_table'].".username
		order by username";
$result = $GLOBALS['connection']->Execute($sql);
?>
<script language="JavaScript" type="text/javascript">
<!--
function ChangePeriod() {
	document.period_form.submit();
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php?page=information"><?php echo $STRING['title_information']?></a> /
	<?php echo $STRING['login_statistic']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_login.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['login_statistic']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
				<form name="period_form" action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
					<?php echo $STRING['period'].$STRING['colon']?>
					<select size="1" name="period" onChange="return ChangePeriod();">
<?php
$period_array = array("latest_week", "latest_month", "latest_half_year", "latest_year");
for ($i = 0; $i < sizeof($period_array); $i++) {
	if ($_GET['period'] == $period_array[$i]) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$period_array[$i].'" '.$selected.'>'.$STRING[$period_array[$i]].'</option>';
}
?>

					</select>
					<input type="submit" name="B1" value="<?php echo $STRING['button_go']?>" class="button">
				</form>
			</td>
			<td nowrap align="right" valign="bottom">
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
				<td width="100" align="center" class="title"><?php echo $STRING['username']?></td>
				<td width="500" align="center" class="title"><?php echo $STRING['login_times']?></td>
			</tr>
<?php

$style_count = 0;
while ($row = $result->FetchRow()) {
	$username = $row['username'];
	$count = $row['count'];
	$table_width = ($count_max?floor(100*($count/$count_max)):100)."%";
	if ($table_width == "0%") {
		$table_width = "1%";
	}
	$td_class = "line".($style_count%2);
	$style_count++;
	echo '
			<tr>
				<td width="100" class="'.$td_class.'" align="center">'.$username.'</td>
				<td width="490" class="'.$td_class.'">
					<table width="'.$table_width.'" height="10" cellpadding="0" cellspacing="0">
					<tr>
						<td background="'.$GLOBALS["SYS_URL_ROOT"].'/images/percent.gif" width="100%" nowrap></td>
						<td nowrap><img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/percent_end.gif" align="middle">'.$count.'</td>
					</tr>
					</table>
				</td>
			</tr>';
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
