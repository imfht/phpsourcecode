<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: statistic_status.php,v 1.18 2013/07/05 21:28:00 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");
include("../include/status_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_statistic'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$project_array = GetAllProjects($_SESSION[SESSION_PREFIX.'uid']);
$total_project = count($project_array);
if ($_GET['project_id'] == "") {
	if ($total_project > 0) {
		$_GET['project_id'] = $project_array[0]->getprojectid();
	}
}

if (($_GET['priority'] != "") && ($_GET['priority'] != -1)){
	$priority_sql = "and priority=".$GLOBALS['connection']->QMagic($_GET['priority']);
} else {
	$priority_sql = "";
}
$status_array = GetStatusArray();

$count_max = 0;
$count_closed = 0;
$count_active = 0;
if ($total_project) {
	$show_array = array();
	for ($i = 0; $i < sizeof($status_array); $i++) {
		$sql = "select count(report_id) from proj".$_GET['project_id']."_report_table
				where status=".$status_array[$i]->getstatusid()." $priority_sql";
		$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
		$count = $result->fields[0];
		if ($count_max < $count) {
			$count_max = $count;
		}
		if ($status_array[$i]->getstatustype() == "closed") {
			$count_closed += $count;
		} else {
			$count_active += $count;
		}
		$the_array = array($status_array[$i]->getstatusname()=>$count);
		$show_array = array_merge($show_array, $the_array);
	} 
	
	if ($count_closed > $count_active) {
		$type_max = $count_closed;
	} else {
		$type_max = $count_active;
	}
	
	if ($count_closed == 0) {
		$closed_width = "1%";
	} else {
		$closed_width = floor(100*($count_closed/$type_max))."%";
	}
	if ($count_active == 0) {
		$active_width = "1%";
	} else {
		$active_width = floor(100*($count_active/$type_max))."%";
	}
} else {
	$closed_width = "1%";
	$active_width = "1%";
}
?>
<script language="JavaScript" type="text/javascript">
<!--
function Change() {
	document.status_form.submit();
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php?page=information"><?php echo $STRING['title_information']?></a> /
	<?php echo $STRING['statistic_status']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_statistic_status.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['statistic_status']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
				<form name="status_form" action="<?php echo $_SERVER['PHP_SELF']?>" method="GET">
<?php
echo $STRING['project'].$STRING['colon'];
echo '
					<select size="1" name="project_id" onChange="return Change();">';
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
					</select>&nbsp;&nbsp;

					'.$STRING['priority'].$STRING['colon'].'
					<select size="1" name="priority" onChange="return Change();">
						<option value="-1">'.$STRING['all_priorities'].'</option>';
for ($i = 0; $i < sizeof($GLOBALS['priority_array']); $i++) {
	if (($_GET['priority'] != "") && ($_GET['priority'] == $i)) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	if ($i == 0) {
		echo '
						<option value="'.$i.'" '.$selected.'>'.$STRING['empty_priorities'].'</option>';
	} else {
		echo '
						<option value="'.$i.'" '.$selected.'>'.$STRING[$GLOBALS['priority_array'][$i]].'</option>';
	}
	
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
				<td width="180" align="center" class="title"><?php echo $STRING['status']?></td>
				<td width="520" align="center" class="title"><?php echo $STRING['count_number']?></td>
			</tr>
<?php
if ($total_project) {
	$args = array_keys($show_array);
	for ($i = 0; $i < count($args); $i++) {
		$key = $args[$i];
		$value = $show_array[$key];

		if ($value == 0) {
			$table_width = 1;
		} else {
			$table_width = floor(100*($value/$count_max));
			if ($table_width == 0) {
				$table_width = "1";
			}
		}
		$td_class = "line".($style_count%2);
		$style_count++;
		echo '
			<tr>
				<td class="'.$td_class.'" align="right" nowrap>'.$key.$STRING['colon'].'</td>
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
}
?>

			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			
			<table class="table-main-list" align="center">
			<tr>	
				<td width="180" align="center" class="title"><?php echo $STRING['type']?></td>
				<td width="720" align="center" class="title"><?php echo $STRING['count_number']?></td>
			</tr>
			<tr>
				<td class="line0" align="right" nowrap>active</td>
				<td class="line0">
					<table width="<?php echo $active_width?>" height="10" cellpadding="0" cellspacing="0">
					<tr>
						<td background="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/percent.gif" width="100%" nowrap></td>
						<td nowrap>
<?php
	if ($count_active != 0) {
		echo '
							<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/percent_end.gif" align="middle">';
	}
	echo $count_active;
?>
	      
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="line1" align="right" nowrap>closed</td>
				<td class="line1">
					<table width="<?php echo $closed_width?>" height="10" cellpadding="0" cellspacing="0">
					<tr>
						<td background="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/percent.gif" width="100%" nowrap></td>
						<td nowrap>
<?php
	if ($count_closed != 0) {
		echo '
							<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/percent_end.gif" align="middle">';
	}
	echo $count_closed;
?>
	      
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php

include("../include/tail.php");
?>
