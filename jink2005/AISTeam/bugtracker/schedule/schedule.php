<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: schedule.php,v 1.21 2013/07/05 20:17:48 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/project_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_schedule'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

function GetDaysInMonth($year, $month)
{
	for ($i = 28; $i <= 31; $i++) {
		if (!checkdate($month, $i, $year)) {break;}
	}
	return $i-1;
}

class schedule_class {
	var $schedule_id;
	var $date;
	var $subject;
	var $created_by;
	var $project_id;
	var $description;

	function set_schedule_id($id) {
		$this->schedule_id = $id;
	}
	function set_date($date) {
		$this->date = $date;
	}
	function set_subject($subject) {
		$this->subject = $subject;
	}
	function set_created_by($created_by) {
		$this->created_by = $created_by;
	}
	function set_project_id($project_id) {
		$this->project_id = $project_id;
	}
	function set_description($description) {
		$this->description = $description;
	}
	function get_schedule_id() {
		return $this->schedule_id;
	}
	function get_date() {
		return $this->date;
	}
	function get_subject() {
		return $this->subject;
	}
	function get_created_by() {
		return $this->created_by;
	}
	function get_project_id() {
		return $this->project_id;
	}
	function get_description() {
		return $this->description;
	}
}

/* Prepare data for database access */
if ($_GET['init'] == "y") {
	unset($_SESSION[SESSION_PREFIX.'schedule_month']);
}
// Initial date
if (isset($_GET["year"]) && isset($_GET["month"])) {
	$_SESSION[SESSION_PREFIX.'schedule_month'] = $_GET["year"]."/".$_GET["month"];
}
if (!isset($_SESSION[SESSION_PREFIX.'schedule_month'])) {
	$_SESSION[SESSION_PREFIX.'schedule_month'] = date("Y")."/".date("n");
}

list($year, $month) = explode('/', $_SESSION[SESSION_PREFIX.'schedule_month']);

/* ===================== Start to prepare the array for display ======================== */
/* Get the first day of the month */
$month_start = mktime(0, 0, 0, $month, 1, $year); 

/* Get the offset of the week */
$offset = date('w', $month_start); /* 0: Sunday, 1: Mon, 2: Tue, and so on */

/* Get friendly month name */
$month_name = date('M', $month_start); 

/* Detemine how many days are in the last month. */
if($month == 1){ 
   $num_days_last = GetDaysInMonth(($year -1), 12);
} else { 
   $num_days_last = GetDaysInMonth($year, ($month -1)); 
}
/* Detemine how many days are in the current month. */
$num_days_current = GetDaysInMonth($year, $month); 

/* Put days of last monthe in the array */
for ($i = $offset - 1; $i >= 0; $i--) {
	$days_to_show[] = $num_days_last - $i;
}
/* Put days of this month in the array */
for ($i = 1; $i <= $num_days_current; $i++) {
	$days_to_show[] = $i;
}
/* Put days of next month in the array if total days is less then 35. */
for ($i = 1; $i <= (sizeof($days_to_show) % 7); $i++) {
	$days_to_show[] = $i;
}
/* ===================== End of prepare the array for display ======================== */

if (($month - 1) > 0) {
	$previous_month_year = $year;
	$previous_month_month = $month - 1;
} else {
	$previous_month_year = $year - 1;
	$previous_month_month = 12;
}
if (($month + 1) > 12) {
	$next_month_year = $year + 1;
	$next_month_month = 1;
} else {
	$next_month_year = $year;
	$next_month_month = $month + 1;
}

if ($days_to_show[0] == 1) {
	$from_date = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $month, 1, $year));
} else {
	// has previous month
	$from_date = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $previous_month_month, $days_to_show[0], $previous_month_year));
}

if ($days_to_show[34] > 25) {
	$to_date = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $next_month_month, 1, $next_month_year));
} else {
	// Has next month
	$to_date = $GLOBALS['connection']->DBTimeStamp(mktime(0, 0, 0, $next_month_month, $days_to_show[34]+1, $next_month_year));
}

/* Get project schedule count */
$sql = "select project_id, count(*) from ".$GLOBALS['BR_schedule_table']." 
		where ( (date >= $from_date and date < $to_date) and (project_id > 0) and 
				((publish = 't') or (created_by = ".$_SESSION[SESSION_PREFIX.'uid'].")) )
		group by project_id order by project_id ASC";

$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$project_count_array = array();
while ($row = $sql_result->FetchRow()) {
	$project_id = $row['project_id'];
	$count = $row[1];

	$the_array = array("project".$project_id => $count);
	$project_count_array = array_merge($project_count_array, $the_array);
}

/* Get user schedule count */
$sql = "select created_by, count(*) from ".$GLOBALS['BR_schedule_table']." 
		where ( (date >= $from_date and date < $to_date) and (project_id = 0) and 
				((publish = 't') or (created_by = ".$_SESSION[SESSION_PREFIX.'uid'].")) )
		group by created_by, project_id order by project_id ASC";

$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$user_count_array = array();
while ($row = $sql_result->FetchRow()) {
	$user_id = $row['created_by'];
	$count = $row[1];

	$the_array = array("user".$user_id => $count);
	$user_count_array = array_merge($user_count_array, $the_array);
}

$project_array = GetAllProjects($_SESSION[SESSION_PREFIX.'uid']);
$userarray = GetAllUsers(1, 0);

/* Get all schedules in the month */
if (!isset($_GET["schedule_type"]) || $_GET["schedule_type"] == "all") {
	$sql = "select * from ".$GLOBALS['BR_schedule_table']." where 
			((date >= $from_date and date < $to_date) and 
		     ((publish = 't') or (created_by = ".$_SESSION[SESSION_PREFIX.'uid'].")) ) order by date ASC";
} else if ($_GET["schedule_type"] < 0) {
	// Project schedule
	$project_id = -1 * $_GET["schedule_type"];
	$sql = "select * from ".$GLOBALS['BR_schedule_table']." where 
			((date >= $from_date and date < $to_date) and (project_id = $project_id) and 
		     ((publish = 't') or (created_by = ".$_SESSION[SESSION_PREFIX.'uid'].")) ) order by date ASC";
} else {
	// Personal schedule
	if ($_SESSION[SESSION_PREFIX.'uid'] == $_GET["schedule_type"]) {
		$sql = "select * from ".$GLOBALS['BR_schedule_table']." where 
			((date >= $from_date and date < $to_date) and (created_by = ".$_SESSION[SESSION_PREFIX.'uid'].") and
			 (project_id = 0)) order by date ASC";
	} else {
		$sql = "select * from ".$GLOBALS['BR_schedule_table']." where 
			((date >= $from_date and date < $to_date) and (created_by = ".$GLOBALS['connection']->QMagic($_GET["schedule_type"]).") and 
			 (publish = 't') and (project_id = 0)) order by date ASC";
	}
}

$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);

$schedule_array = array();
while ($row = $sql_result->FetchRow()) {
	$schedule_id = $row['schedule_id'];
	$date = $sql_result->UserTimeStamp($row['date']);
	$subject = $row['subject'];
	$created_by = $row['created_by'];
	$project_id = $row['project_id'];
	$description = $row['description'];

	$new_schedule = new schedule_class;
	$new_schedule->set_schedule_id($schedule_id);
	$new_schedule->set_date($date);
	$new_schedule->set_subject($subject);
	$new_schedule->set_created_by($created_by);
	$new_schedule->set_project_id($project_id);
	$new_schedule->set_description($description);
	array_push($schedule_array, $new_schedule);
}
?>

<script language="JavaScript">
<!--
function ScheduleChange()
{
	var f = document.schedule;

	if (f.schedule_type.options[f.schedule_type.selectedIndex].value == '') {
		return false;
	}
	f.submit();
}

function ScheduleMonth(action)
{
	var f = document.schedule;
	var i, year, month;

	if (action == 'today') {
		year = <?php echo date("Y")?>;
		month = <?php echo date("n")?>;
	} else if (action == 'next') {
		year = <?php echo $next_month_year?>;
		month = <?php echo $next_month_month?>;
	} else {
		year = <?php echo $previous_month_year?>;
		month = <?php echo $previous_month_month?>;
	}

	for (i = 0; i < f.year.length; i++) {
		if (f.year.options[i].value == year) {
			f.year.selectedIndex = i;
		}
	}
	for (i = 0; i < f.month.length; i++) {
		if (f.month.options[i].value == month) {
			f.month.selectedIndex = i;
		}
	}
	return ScheduleChange();
}

function ScheduleCreate(time)
{
	location.href = 'schedule_new.php?time='+time;
	return false;
}
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['title_schedule'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'schedule_delete.php?schedule_id='+id;
				}
				return;
			}
	});
	return;
}

var current_div;
function ScheduleShowAll(time)
{
	var div = document.getElementById('schedule'+time);
	var div_content = document.getElementById('schedule_content'+time);
	var div_close;

	if (!div) {
		return;
	}

	if (current_div) {
		current_div.style.display = 'none';
	}
	current_div = div;

	div.style.top = div_content.offsetTop;
	div.style.left = div_content.offsetLeft;
	div.style.display = 'block';
	
	div_close = document.getElementById('schedule_close'+time);
	div_close.onmouseover = function() {
		this.className = 'aw-dlg-close aw-dlg-close-over';
	};
	div_close.onmouseout = function() {
		this.className = 'aw-dlg-close';
	};
	div_close.onclick = function() {
		div.style.display = 'none';
	};
}
//-->
</script>

<div id="main_container">
	
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_schedule.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['title_schedule']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">

			</td>
		</tr>
	</table>

	<div id="sub_container"  style="width: 900;">
	
		<div class="schedule_container">
			<form action="<?php echo $_SERVER['PHP_SELF']?>" method="get" name="schedule">

			<div style="float:left;text-align:left; position: relative;width:50%;">
				<input name="today" value="<?php echo $STRING['today']?>" type="button" class="schedule_botton" onclick="ScheduleMonth('today');">
			<?php
echo '
				<select size="1" name="year">';
$value_array = array();
$option_array = array();
for ($i = 2005; $i<(date("Y")+5); $i++) {
	$value_array[] = $i;
	$option_array[] = $i;
}
PrintSelectOptions($value_array, $option_array, $year);
echo '
				</select>
				<select size="1" name="month" onChange="return ScheduleChange();">';
$value_array = array();
$option_array = array();
for ($i = 1; $i <= 12; $i++) {
	$value_array[] = $i;
	$option_array[] = $i;
}
PrintSelectOptions($value_array, $option_array, $month);
echo '
				</select>';

?>

				<input type="button" value="<?php echo $STRING['button_go']?>" onClick="return ScheduleChange();" class="schedule_botton">
				<input name="next" value="<<" type="button" class="schedule_botton" onClick="return ScheduleMonth('previous');">
				<input name="next" value=">>" type="button" class="schedule_botton" onClick="return ScheduleMonth('next');">
			</div>
			<div style="float:right; text-align: right; position: relative;width:50%;">
				<font color="#42649b"><?php echo $STRING['schedule_for']?></font>
				<select name="schedule_type" size="1" onchange="return ScheduleChange();">
					<option value="all"><?php echo $STRING['all_schedule']?></option>
<?php

// Show schedule for

$project_option = "";
$project_info = NULL;
for ($i = 0; $i < sizeof($project_array); $i++) {
	$visible_project_id = $project_array[$i]->getprojectid();
	$count = $project_count_array["project".$visible_project_id];
	if ($count == "") {
		$count = 0;
	}
	if ($count === 0) {
		continue;
	}
	$visible_project_id = ($visible_project_id * -1);
	$visiable_project_name = $project_array[$i]->getprojectname();

	if ($_GET["schedule_type"] == $visible_project_id) {
		$project_option .= '
					<option value="'.$visible_project_id.'" selected>'.$visiable_project_name.'('.$count.')</option>';
	} else {
		$project_option .= '
					<option value="'.$visible_project_id.'">'.$visiable_project_name.'('.$count.')</option>';
	}
}
if ($project_option != "") {
	echo '
					<option value="">==== '.$STRING['project_schedule'].' ====</option>';
	echo $project_option;
}
echo '
					<option value="">==== '.$STRING['personal_schedule'].' ====</option>';

for ($i = 0; $i < sizeof($userarray); $i++) {
	$uid = $userarray[$i]->getuserid();
	$count = $user_count_array["user".$uid];
	if ($count == "") {
		$count = 0;
	}
	if ($count === 0) {
		continue;
	}
	if ($_GET["schedule_type"] == $uid) {
		echo '
					<option value="'.$uid.'" selected>'.$userarray[$i]->getusername().'('.$count.')</option>';
	} else {
		echo '
					<option value="'.$uid.'">'.$userarray[$i]->getusername().'('.$count.')</option>';
	}
}

?>

				</select>
			</div>
		
			</form>
		</div>
		<table class="schedule_container">
		<tr>
			<td class="schedule_title"><?php echo $STRING['sunday']?></td>
			<td class="schedule_title"><?php echo $STRING['monday']?></td>
			<td class="schedule_title"><?php echo $STRING['tuesday']?></td>
			<td class="schedule_title"><?php echo $STRING['wednesday']?></td>
			<td class="schedule_title"><?php echo $STRING['thursday']?></td>
			<td class="schedule_title"><?php echo $STRING['friday']?></td>
			<td class="schedule_title"><?php echo $STRING['saturday']?></td>
		</tr>

<?php


$schedule_index = 0;
for ($i = 0; $i < sizeof($days_to_show); $i++) {
	$today_class = '';
	if ($i < $offset) {
		// This is previous month
		$class = "schedule_days_not_in_month";
		$this_time = mktime(0, 0, 0, $previous_month_month, $days_to_show[$i], $previous_month_year);
	} else if (($i > 20) && $days_to_show[$i] < 10) {
		// This is next month
		$class = "schedule_days_not_in_month";
		$this_time = mktime(0, 0, 0, $next_month_month, $days_to_show[$i], $next_month_year);
	} else {
		// This is current month
		$class = "schedule_days_in_month";
		$this_time = mktime(0, 0, 0, $month, $days_to_show[$i], $year);

		/*
		$week = date("D", $this_time);
		if (($week == "Sat") || ($week == "Sun")) {
			$class = "$class schedule_days_holiday";
		}
		*/
		if ( ($year == date("Y")) && ($month == date("n")) && ($days_to_show[$i] == date("j")) ) {
			// Today
			$today_class = 'schedule_content_today';
		}
	}
    
	$the_date = date("Y-m-d", $this_time);
	$show_date = date(GetDateFormat(), $this_time);

	if ($i % 7 == 0) {
		echo '
		<tr>';
	}
	echo '
			<td class="schedule_item">
				<div class="schedule_date '.$class.'" style="cursor:pointer;"   onclick="ScheduleCreate('.$this_time.')" onMouseOver="this.style.backgroundColor=\'#FFFFCC\'"  onMouseOut="this.style.backgroundColor=\'\'">
					<a title="">'.$days_to_show[$i].'</a> 
				</div> 
				<div id="schedule_content'.$this_time.'" class="schedule_content '.$today_class.'">';

	$schedules = array();
	for (; $schedule_index < sizeof($schedule_array); $schedule_index++) {
		list($the_year, $the_month, $the_day) = explode('-', substr($schedule_array[$schedule_index]->get_date(), 0, 10));
		if ($this_time == mktime(0, 0, 0, $the_month, $the_day, $the_year)) {
			array_push($schedules, $schedule_array[$schedule_index]);
		} else {
			// The $schedule_array is sorted
			break;
		}
	}

	$total = sizeof($schedules);
	$displayed = 0;
	$available = 0;
	if ($total > 0) {
		$more_mesg = '';
		$skip_output = 0;
		for ($j = 0; $j < $total;$j++) {
			if ($displayed == 4) {
				$skip_output = 1;
			}
			$the_schedule = $schedules[$j];
			$mesg = $STRING['subject'].$STRING['colon'].' '.$the_schedule->get_subject().'<br>';
			$mesg .= $STRING['created_by'].$STRING['colon'].' '.UidToUsername($userarray, $the_schedule->get_created_by()).'<br>';
				
			if ($the_schedule->get_project_id() > 0) {
				$project = GetProjectFromID($project_array, $the_schedule->get_project_id());
				if (!$project) continue;
				$title = addslashes($STRING['project_schedule']);
				$mesg .= $STRING['project'].$STRING['colon'].' '.$project->getprojectname().'<br>';
			} else {
				$title = addslashes($STRING['personal_schedule']);
			}
			if (($_SESSION[SESSION_PREFIX.'uid'] == 0) || ($the_schedule->get_created_by() == $_SESSION[SESSION_PREFIX.'uid'])) {
				$action_mesg = '<hr><div style="text-align:left">';
				$action_mesg .= '<a href="schedule_edit.php?schedule_id='.$the_schedule->get_schedule_id().'">';
				$action_mesg .= '<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/edit.gif" width="16" height="16" align="middle" border="0" title="'.$STRING['edit'].'">';
				$action_mesg .= '</a> ';
				$action_mesg .= '<a href="JavaScript:ConfirmDelete('.$the_schedule->get_schedule_id().');">';
				$action_mesg .= '<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/delete.gif" width="16" height="16" align="middle" border="0" title="'.$STRING['delete'].'">';
				$action_mesg .= '</a>';
				$action_mesg .= '</div>';
				//$action_mesg = htmlspecialchars($action_mesg);
			}
			$replace_order   = array("\r\n", "\n", "\r");
			$full_mesg = $mesg."<br>".str_replace('%', '%25', str_replace($replace_order, "<br>", $the_schedule->get_description()));
			$full_mesg .= $action_mesg;
			$full_mesg = addslashes(htmlspecialchars($full_mesg));
			$mesg = addslashes(htmlspecialchars($mesg));
			$output = '
				<div onmouseover="return ALEXWANG.Tooltip.Show({title:\''.$title.'\',msg:\''.$mesg.'\',width:250});" onmouseout="ALEXWANG.Tooltip.Hide();">
					<a href="JavaScript:ALEXWANG.Dialog.Show({title: \''.$title.'\', msg:\''.$full_mesg.'\',width:400, buttons: [\'ok\']});">
						'.$the_schedule->get_subject().'
					</a>
				</div>';
			
			if ($skip_output == 0) {
				$displayed++;
				echo $output;
			}
			$available++;
			
			$more_mesg .= $output;
		}
		if ($displayed != $available) {
			echo '
				
				<div style="text-align:center">
					<a href="JavaScript:ScheduleShowAll(\''.$this_time.'\');">+'.($available - $displayed).' More</a>
				</div>';
				
		}
	}


	echo '
				</div>
		<div id="schedule'.$this_time.'" class="schedule_show_all">
			<div id="schedule_close'.$this_time.'" class="aw-dlg-close"></div>
			<div class="schedule_show_all_hd"></div>
			<div class="schedule_show_all_mc">'.$more_mesg.'</div>
		</div>
			</td>';
	if ($i % 7 == 6) {
		echo '
		</tr>';
	}
}
?>

		</table>
	</div>
</div>

<?php
PrintGotoTop();
include("../include/tail.php");
?>
