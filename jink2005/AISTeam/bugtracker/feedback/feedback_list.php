<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: feedback_list.php,v 1.22 2013/07/07 21:25:52 alex Exp $
 *
 */
include("../include/header.php");
include("../include/customer_function.php");
include("../include/project_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_feedback'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

// Get project data
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$project_line = $project_result->Recordcount();
if ($project_line == 1) {
	$project_name = $project_result->fields["project_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

if (!$_GET['sort_by']) {
	$sort_by = "report_id";
} else {
    if (false === strpos($_GET['sort_by'], ';') && false === strpos($_GET['sort_by'], ' ')) {
		$sort_by = $_GET['sort_by'];
	}
}

if (!$_GET['sort_method']) {
	$sort_method = "DESC";
} else {
	$sort_method = $_GET['sort_method'];
}

if ($sort_method != "DESC") {
	$sort_method = "ASC";
}

if (!$_GET['page']) {
	$page = 1;
} else {
	$page = $_GET['page'];
}

$search_key = trim($_GET['search_key']);
if ($search_key == "") {
	unset($search_key);
}

// Jump to specific project ID by search #ID
if ( isset($search_key) && (preg_match("/^#+[0-9]+[0-9]*$/", $search_key))) {
	$search_report_id = substr($_GET['search_key'], 1);
	$get_report_sql = "select * from proj".$_GET['project_id']."_feedback_table 
				where report_id=".$GLOBALS['connection']->QMagic($search_report_id);
	$get_report_result = $GLOBALS['connection']->Execute($get_report_sql) or DBError(__FILE__.":".__LINE__);
	$get_report_line = $get_report_result->Recordcount();
	if ($get_report_line == 1) {
		echo "<h2 align=\"center\">Report found! Please see <a href=\"feedback_report_show.php?project_id=".$_GET['project_id']."&report_id=$search_report_id\">here</a></h2>";
		echo "<script>";
		echo "location.href = \"feedback_report_show.php?project_id=".$_GET['project_id']."&report_id=".$search_report_id."\";";
		echo "</script>";
		exit;
	} else {
		$_GET['search_key'] = "";
		ErrorPrintBackFormOut("GET", "feedback_list.php?project_id=".$_GET['project_id'], $_GET, 
							  "no_such_xxx", "report");
	}
}
   
// ��o�ϥΪ̪��]�w�A�]�t�C����ܴX���B�n��ܭ������
$setting_query = "select * from ".$GLOBALS['BR_user_table']." 
			where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$setting_result = $GLOBALS['connection']->Execute($setting_query) or DBError(__FILE__.":".__LINE__);
$line = $setting_result->Recordcount();
$setting_row = $setting_result->FetchRow();
   
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "user", __FILE__.":".__LINE__);
	ErrorPrintOut("no_setting");
}
	
$perpage = $setting_row["perpage"];
if (!$perpage) {$perpage=100;}
$default_filter = $setting_row["default_filter"];

$startat = ($page-1) * $perpage;

$condition = "";
if (($_GET['customer_filter'] != "") && ($_GET['customer_filter'] != "-1")){
	$condition = "customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_filter']);
}

$search_condition = "";
if (isset($search_key)) {
	$summary_match="(summary ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$search_key."%").")";
	$log_match="(description ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$search_key."%").")";
	if (strstr($search_key, " and ")) {   
		$summary_match = str_replace(" and ","%' and summary ".PATTERN_KEYWORD." '%",$summary_match);
		$log_match = str_replace(" and ","%' and description ".PATTERN_KEYWORD." '%",$log_match);
	}
	if (strstr($search_key, " not ")) {
		$summary_match = str_replace(" not ","%' and summary not ".PATTERN_KEYWORD." '%",$summary_match);
		$log_match = str_replace(" not ","%' and description not ".PATTERN_KEYWORD." '%",$log_match);
	}
	if (strstr($search_key, " or ")) {
		$summary_match = str_replace(" or ","%' or summary ".PATTERN_KEYWORD." '%",$summary_match);
		$log_match=str_replace(" or ","%' or description ".PATTERN_KEYWORD." '%",$log_match);
	}
	$search_condition = $summary_match." or ( report_id in 
			(select report_id from proj".$_GET['project_id']."_feedback_content_table where ".$log_match."
			 group by report_id))";
}

if ($condition == "") {
	if ($search_condition == "") {
		$condition = "";
	} else {
		$condition = "where ".$search_condition;
	}
} else {
	if ($search_condition == "") {
		$condition = "where ".$condition;
	} else {
		$condition = "where (".$search_condition.") and ".$condition;
	}
}

$count_sql = "SELECT count(report_id) FROM 
			proj".$_GET['project_id']."_feedback_table $condition";

$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

$allsql="SELECT * FROM 
		proj".$_GET['project_id']."_feedback_table
		$condition ORDER BY $sort_by $sort_method";

$allposts = $GLOBALS['connection']->SelectLimit($allsql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);

?>

<form method="GET" name="main_form" action="<?php echo $_SERVER['PHP_SELF']?>">
	<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
	<input type="hidden" name="search_key" value="<?php echo stripslashes($_GET['search_key'])?>">
	<input type="hidden" name="page" value="<?php echo stripslashes($_GET['page'])?>">
	<input type="hidden" name="customer_filter" value="<?php echo stripslashes($_GET['customer_filter'])?>">
	<input type="hidden" name="sort_by" value="<?php echo stripslashes($_GET['sort_by'])?>">
	<input type="hidden" name="sort_method" value="<?php echo stripslashes($_GET['sort_method'])?>">
	<input type="hidden" name="report_id" value="-1">
<?php
$column_array = array("created_date", "type", "priority", "status");
$column_num = sizeof($column_array);

for ($i=0; $i<sizeof($column_array); $i++) {
    echo '
	<input type="hidden" name="'.$column_array[$i].'" value="'.$_GET[$column_array[$i]].'">';
}
?>

</form>

<div style="display: none;" id="local_search_container">
	<form method="get" name="search_form" action="<?php echo $_SERVER['PHP_SELF']?>" OnSubmit="return OnSubmit(this);">
<?php
PrintTip($STRING['hint_title'], $STRING['search_hint']);
$_GET['search_key'] = str_replace('"', "&quot;", $_GET['search_key']);
?>
		<font color="#42649B"><?php echo $STRING['search'].$STRING['colon']?></font>        
		<input class="input-form-text-field" type="text" name="search_key" value="<?php echo stripslashes(rawurldecode($_GET['search_key']))?>" size="25" maxlength="64">
		<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
		<input type="submit" class="button" value="<?php echo $STRING['button_go']?>" name="B1">
	</form>
</div>

<script language="JavaScript">
<!--
function AddSearchBox()
{
	// The search_container is in header
	var container = document.getElementById('search_container');
	var local_container = document.getElementById('local_search_container');
	if (!container) {
		return false;
	}
	container.innerHTML = local_container.innerHTML;
}
AddSearchBox();

function DocumentReload()
{
	form = document.main_form;

	if (form.search_key.value == '') {
		form.search_key.disabled = true;
	} else {
		form.search_key.disabled = false;
	}

	if (form.customer_filter.value == '') {
		form.customer_filter.disabled = true;
	} else {
		form.customer_filter.disabled = false;
	}

	if (form.sort_by.value == '') {
		form.sort_by.disabled = true;
	} else {
		form.sort_by.disabled = false;
	}

	if (form.sort_method.value == '') {
		form.sort_method.disabled = true;
	} else {
		form.sort_method.disabled = false;
	}

	if ((form.page.value == '') || (form.page.value == -1)) {
		form.page.disabled = true;
	} else {
		form.page.disabled = false;
	}

	if ((form.report_id.value == '') || (form.report_id.value == -1)) {
		form.report_id.disabled = true;
	} else {
		form.report_id.disabled = false;
	}
<?php
for ($i=0; $i<sizeof($column_array); $i++) {
	echo '
	if (form.'.$column_array[$i].' && (form.'.$column_array[$i].'.value == -1 || form.'.$column_array[$i].'.value == \'\')) {
		form.'.$column_array[$i].'.disabled = true;
	} else {
		form.'.$column_array[$i].'.disabled = false;
	}
';
}
?>
	form.submit();
}
function ChangeFilter()
{
	document.main_form.customer_filter.value = document.filter_form.customer_filter.options[document.filter_form.customer_filter.selectedIndex].value;
	document.main_form.page.value = -1;
	DocumentReload();
}
function ChangeSort(sort_by, sort_method)
{
	document.main_form.sort_by.value = sort_by;
	document.main_form.sort_method.value = sort_method;
	document.main_form.page.value = -1;
	DocumentReload();
}
function ChangePage(page)
{
	document.main_form.page.value = page;
	DocumentReload();
}
function ChangeURL(url, report_id, blank)
{
	var old_url = document.main_form.action;
	document.main_form.action = url;

	if (report_id != -1) {
		document.main_form.report_id.value = report_id;
	}
	
	if (blank) {
		document.main_form.target = '_blank';
	}
	DocumentReload();
	document.main_form.action = old_url;
	document.main_form.target = '_self';
}

function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
		msg: '<?php echo addslashes(str_replace("@key@", $STRING['report'], $STRING['delete_note']));?>',
		buttons: ['yes', 'no'],
		width: 300,
		fn: function(button) {
			if (button == 'yes') {
				ChangeURL('feedback_report_delete.php', id, false);
			}
			return;
		}
	});
	return;
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../index.php"><?php echo $STRING['title_project_list']?></a> / <?php echo htmlspecialchars($project_name)." ".$STRING['title_feedback']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="feedback_list.php?project_id=<?php echo $_GET['project_id']?>">
					<tt class="outline"><?php echo htmlspecialchars($project_name)." ".$STRING['title_feedback']?></tt>
				</a>
			</td>
			<td nowrap valign="bottom" align="center" width="100%">
				<form method="GET" name="filter_form" action="feedback_list.php">
					<font color="#42649B"><?php echo $STRING['customer_filter'].$STRING['colon']?></font>
					<select size="1" name="customer_filter" onChange="ChangeFilter();">
						<option value="-1"><?php echo $STRING['all_customers']?></option>
<?php
$customer_array = GetAllCustomers();

for ($i = 0; $i < sizeof($customer_array); $i++) {
	$customer_id = $customer_array[$i]->getcustomerid();
	$customer_name = $customer_array[$i]->getcustomername();
	if ($customer_id == $_GET['customer_filter']) {
		echo '
						<option value="'.$customer_id.'" selected>'.$customer_name.'</option>';
	}else{
		echo '
						<option value="'.$customer_id.'">'.$customer_name.'</option>';
	}
}

?>
					</select>
				</form>
			</td>
			<td nowrap valign="bottom" align="right">
				<a href="../index.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">

		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<table class="table-main-list" align="center">
		<tr>
			<td width="10">&nbsp;</td>
			<td width="100%" align="center" colspan="<?php echo ($column_num+5)?>">
<?php                    
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");

if ($sort_method == "DESC") {$sort_method = "ASC";} else {$sort_method = "DESC";}
?>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="title">&nbsp;</td>
			<td width="25" class="title" align="center" nowrap><font size=2><a href="JavaScript:ChangeSort('report_id', '<?php echo $sort_method?>');">ID1</a></font></td>
			<td width="25" class="title" align="center" nowrap><font size=2><a href="JavaScript:ChangeSort('cust_report_id', '<?php echo $sort_method?>');">ID2</a></font></td>
			<td width="40%" class="title" align="center"><font size=2><a href="JavaScript:ChangeSort('summary', '<?php echo $sort_method?>');"><?php echo $STRING['summary']?></a></font></td>
			<td class="title" align="center" nowrap><font size=2><a href="JavaScript:ChangeSort('customer_id', '<?php echo $sort_method?>');"><?php echo $STRING['customer']?></a></font></td>
<?php
for ($i = 0; $i < sizeof($column_array); $i++) {
	echo '
			<td class="title" align="center" nowrap>
				<a href="JavaScript:ChangeSort(\''.$column_array[$i].'\', \''.$sort_method.'\');">
				'.$STRING[$column_array[$i]].'</font></a>
			</td>';
}

if ($sort_method=="DESC") {$sort_method="ASC";} else {$sort_method="DESC";}

echo '
			<td width="60" class="title" align="center" nowrap>'.$STRING['function'].'</td>
			<td width="10" class="title" align="center">&nbsp;</td>
		</tr>';

$show_in_blank="false";
if ($setting_row["show_in_blank"] == 't') {
	$show_in_blank="true";
}

$num = 0;
while ($row = $allposts->FetchRow()) {
	$report_id = $row["report_id"];
	$cust_report_id = $row["cust_report_id"];
	$summary = $row["summary"];
	$customer_id = $row["customer_id"];
	$td_class = "line".($num%2);
	$num++;

	echo '
		<tr>
			<td align="center" class="'.$td_class.'">
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
			</td>
			<td align="center" class="'.$td_class.'">'.$report_id.'</td>
			<td align="center" class="'.$td_class.'">'.$cust_report_id.'</td>
			<td align="left" class="'.$td_class.'">
				<a href="JavaScript:ChangeURL(\'feedback_report_show.php\', '.$report_id.', '.$show_in_blank.');">'.$summary.'</a>
			</td>
			<td align="center" class="'.$td_class.'">'.GetCustomerNameFromID($customer_array, $customer_id).'</td>';

	for ($i = 0; $i < sizeof($column_array); $i++) {
		$column_value = $row[$column_array[$i]];

		echo '
			<td class="'.$td_class.'" align="center">';
		if ($column_array[$i] == "priority") {
			$priority = $STRING[$GLOBALS['priority_array'][$column_value]];
			echo "<font color=".$GLOBALS['priority_color'][$column_value].">$priority</font> \n";

		} elseif ($column_array[$i] == "type") {
			echo $STRING[$GLOBALS['type_array'][$column_value]];
		} elseif ($column_array[$i] == "status") {

			$status = $GLOBALS['feedback_status'][$column_value];
			$color = $GLOBALS['feedback_status_color'][$column_value];
			echo "<font color=\"".$color."\"> \n";
			echo $status."</font> \n";

		} elseif ($column_array[$i] == "created_date") {
			$column_value = $allposts->UserTimeStamp($column_value, GetDateFormat());
			echo "$column_value\n";

		} else {
			echo "$column_value \n";
		}
		echo '
			</td>';
	} /* for each column */
      
	echo '
			<td align="center" width="60" class="'.$td_class.'">
				<a href="JavaScript:ChangeURL(\'feedback_report_update.php\', '.$report_id.', false);">'.$STRING['update'].'</a><br>
				<a href="JavaScript:ConfirmDelete('.$report_id.');">'.$STRING['delete'].'</a>
			</td>
			<td width="10" align="center" class="'.$td_class.'">&nbsp;</td>
		</tr>';
}// end of for each report
?>
		<tr>
			<td width="10" valign="bottom">&nbsp;</td>
			<td width="100%" align="center" colspan="<?php echo ($column_num+5)?>">
<?php
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");
?>

			</td>
		</tr>
		</table>

		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
PrintGotoTop();

include("../include/tail.php");
?>

