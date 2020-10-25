<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: statistic_new_report.php,v 1.11 2008/11/28 10:36:31 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_statistic'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_GET['period'] == "") {
	$_GET['period'] = "period_day";
}

$project_array = GetAllProjects($_SESSION[SESSION_PREFIX.'uid']);
$total_project = count($project_array);
if ($_GET['project_id'] == "") {
	if ($total_project > 0) {
		$_GET['project_id'] = $project_array[0]->getprojectid();
	}
	
}

$count_max = 0;
if ($_GET['period'] == "period_day") {
	$timestamp = mktime(0, 0, 0, date("m"), date("d"), date("y")) + 86400;
	$show_array = array();
	if ($total_project) {
		for ($i = 0; $i < 30; $i++) {
			$tomorrow = $GLOBALS['connection']->DBTimeStamp($timestamp);
			$today = $GLOBALS['connection']->DBTimeStamp($timestamp - 86400);
			$sql = "select count(report_id) from proj".$_GET['project_id']."_report_table
					where created_date >= $today and created_date < $tomorrow";
			$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
			$count = $result->fields[0];
			if ($count_max < $count) {
				$count_max = $count;
			}
			$today = $GLOBALS['connection']->UserTimeStamp($timestamp - 86400, GetDateFormat());
			$the_array = array($today=>$count);
			$show_array = array_merge($show_array, $the_array);
			$timestamp -= 86400;
		}
	}
} else if ($_GET['period'] == "period_week") {
	$day = date("w");
	$timestamp = mktime(0, 0, 0, date("m"), date("d"), date("y")) + ((7-$day) * 86400);
	$show_array = array();
	if ($total_project) {
		for ($i = 0; $i < 24; $i++) {
			$next_week = $GLOBALS['connection']->DBTimeStamp($timestamp);
			$this_week = $GLOBALS['connection']->DBTimeStamp($timestamp - 7*86400);
			$sql = "select count(report_id) from proj".$_GET['project_id']."_report_table
					where created_date >= $this_week and created_date < $next_week";
			$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
			$count = $result->fields[0];
			if ($count_max < $count) {
				$count_max = $count;
			}
			$key = date("M d", $timestamp - 7*86400)." ~ ".date("M d", $timestamp - 86400);
			$the_array = array($key=>$count);
			$show_array = array_merge($show_array, $the_array);
			$timestamp -= 7*86400;
		}
	}
} else if ($_GET['period'] == "period_month") {
	$month = date("n");
	$show_array = array();
	if ($total_project) {
		for ($i = 0; $i < 24; $i++) {
			$next_month = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $month+1, 1, date("Y")));
			$this_month = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $month, 1, date("Y")));
			$sql = "select count(report_id) from proj".$_GET['project_id']."_report_table
					where created_date >= $this_month and created_date < $next_month";
			$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
			$count = $result->fields[0];
			if ($count_max < $count) {
				$count_max = $count;
			}
			$key = date("M, Y", mktime(0, 0, 0, $month, 1, date("Y")));
			$the_array = array($key=>$count);
			$show_array = array_merge($show_array, $the_array);
			$month--;
		}
	}
}

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
	<?php echo $STRING['statistic_new_report']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_statistic_new_report.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['statistic_new_report']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
				<form name="period_form" action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
<?php
echo $STRING['project'].$STRING['colon'];
echo '
					<select size="1" name="project_id" onChange="return ChangePeriod();">';
for ($i = 0; $i<count($project_array); $i++) {
	if ($project_array[$i]->getprojectid() == $_GET['project_id']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$project_array[$i]->getprojectid().'" '.$selected.'>
							'.$project_array[$i]->getprojectname().'
						</option>';
}
echo '
					</select>&nbsp;&nbsp;';

echo $STRING['period'].$STRING['colon'];
echo '
					<select size="1" name="period" onChange="return ChangePeriod();">';
$period_array = array("period_day", "period_week", "period_month");
for ($i = 0; $i < sizeof($period_array); $i++) {
	if ($_GET['period'] == $period_array[$i]) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$period_array[$i].'" '.$selected.'>'.$STRING[$period_array[$i]].'</option>';
}
echo '
					</select>';
?>
					
					<input type="submit" name="B1" value="<?php echo $STRING['button_go']?>" class="button">
				</form>
			</td>
			<td nowrap valign="bottom">
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
				<td width="140" align="center" class="title"><?php echo $STRING['period']?></td>
				<td width="520" align="center" class="title"><?php echo $STRING['count_number']?></td>
			</tr>
<?php
$args = array_keys($show_array);
for ($i = 0; $i < count($args); $i++) {
	$key = $args[$i];
	$value = $show_array[$key];
	
	if ($value == 0) {
		$table_width = "1";
	} else {
		$table_width = floor(100*($value/$count_max));
		if ($table_width == 0) {
			$table_width = 1;
		}
		
	}
	$td_class = "line".($style_count%2);
	$style_count++;

	echo '
			<tr>
				<td class="'.$td_class.'" align="center" nowrap>'.$key.'</td>
				<td class="'.$td_class.'">
					<table width="'.$table_width.'%" height="10" cellpadding="0" cellspacing="0">
					<tr>
						<td background="'.$GLOBALS["SYS_URL_ROOT"].'/images/percent.gif" width="100%" nowrap></td>
						<td nowrap>';
	if ($value != 0) {
		echo '
							<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/percent_end.gif" align="middle">';
	}
	echo '
							'.$value.'
						</td>
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
