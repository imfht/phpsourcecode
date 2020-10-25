<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_list.php,v 1.19 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/project_function.php");
include("include/datetime_function.php");

AuthCheckAndLogin();

$filter_column_array = array("type", "priority", "status");
$column_array = array("created_by", "created_date", "type", "priority", "status");
$column_num = sizeof($column_array);

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
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

// �n�H��ؤ覡�Ƨ�
if (!$_GET['sort_by']) {
	$sort_by = "report_id";
} else {
    // Avoid SQL injection
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
				where cust_report_id=".$GLOBALS['connection']->QMagic($search_report_id)." and 
				customer_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_customer']);
	$get_report_result = $GLOBALS['connection']->Execute($get_report_sql) or DBError(__FILE__.":".__LINE__);
	$get_report_line = $get_report_result->Recordcount();
	if ($get_report_line == 1) {
		$report_id = $get_report_result->fields["report_id"];
		echo "<h2 align=\"center\">Report found! Please see <a href=\"report_show.php?project_id=".$_GET['project_id']."&report_id=$report_id\">here</a></h2>";
		echo "<script>";
		echo "location.href = \"report_show.php?project_id=".$_GET['project_id']."&report_id=".$report_id."\";";
		echo "</script>";
		exit;
	} else {
		$_GET['search_key'] = "";
		ErrorPrintBackFormOut("GET", "project_list.php?project_id=".$_GET['project_id'], $_GET, 
							  "no_such_xxx", "report");
	}
}
   
$perpage = ITEMS_PER_PAGE;
if (!$perpage) {$perpage=100;}

$startat = ($page-1) * $perpage;

$search_condition = "";
if (isset($search_key)) {
	$summary_match="(summary ".PATTERN_KEYWORD." '%".$search_key."%')";
	$log_match="(description ".PATTERN_KEYWORD." '%".$search_key."%')";
	if (strstr($search_key, " and ")) {   
		$summary_match=str_replace(" and ","%' and summary ".PATTERN_KEYWORD." '%",$summary_match);
		$log_match=str_replace(" and ","%' and description ".PATTERN_KEYWORD." '%",$log_match);
	}
	if (strstr($search_key, " not ")) {
		$summary_match=str_replace(" not ","%' and summary not ".PATTERN_KEYWORD." '%",$summary_match);
		$log_match=str_replace(" not ","%' and description not ".PATTERN_KEYWORD." '%",$log_match);
	}
	if (strstr($search_key, " or ")) {
		$summary_match=str_replace(" or ","%' or summary ".PATTERN_KEYWORD." '%",$summary_match);
		$log_match=str_replace(" or ","%' or description ".PATTERN_KEYWORD." '%",$log_match);
	}
	$search_condition = $summary_match." or ( report_id in 
			(select report_id from proj".$_GET['project_id']."_feedback_content_table where ".$log_match."
			 group by report_id))";
}


for ($i=0; $i<sizeof($filter_column_array); $i++) {
	if (isset($_GET[$filter_column_array[$i]])) {
		if ($quick_filter != "") {
			$quick_filter .= " and ";
		}
		$quick_filter .= $filter_column_array[$i]."='".$_GET[$filter_column_array[$i]]."'";
	}
}

if ($search_condition != "") {
	if ($quick_filter != "") {
		$condition = "where customer_id='".$_SESSION[SESSION_PREFIX.'feedback_customer']."' and ($search_condition) and (".$quick_filter.")";
	} else {
		$condition = "where customer_id='".$_SESSION[SESSION_PREFIX.'feedback_customer']."' and ($search_condition)";
	}
} else if ($quick_filter != "") {
	$condition = "where customer_id='".$_SESSION[SESSION_PREFIX.'feedback_customer']."' and (".$quick_filter.")";
} else {
	$condition = "where customer_id='".$_SESSION[SESSION_PREFIX.'feedback_customer']."'";
}

$count_sql = "SELECT count(report_id) FROM 
			proj".$_GET['project_id']."_feedback_table $condition";

$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

$allsql="SELECT * FROM 
		proj".$_GET['project_id']."_feedback_table
		$condition ORDER BY $sort_by $sort_method";

$allposts = $GLOBALS['connection']->SelectLimit($allsql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);

/* remove me */
$extra_params = GetExtraParams($_GET, "search_key");
?>
<form method="GET" name="main_form" action="project_list.php">
<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
<input type="hidden" name="search_key" value="<?php echo stripslashes($_GET['search_key'])?>">
<input type="hidden" name="page" value="<?php echo stripslashes($_GET['page'])?>">
<input type="hidden" name="sort_by" value="<?php echo stripslashes($_GET['sort_by'])?>">
<input type="hidden" name="sort_method" value="<?php echo stripslashes($_GET['sort_method'])?>">
<input type="hidden" name="report_id" value="-1">
<?php
for ($i=0; $i<sizeof($filter_column_array); $i++) {
	echo '<input type="hidden" name="'.$filter_column_array[$i].'" value="'.$_GET[$filter_column_array[$i]].'">';
	echo "\n";
}
?>
</form>

<div style="display: none;" id="local_search_container">
	<form method="get" name="search_form" action="<?php echo $_SERVER['PHP_SELF']?>" onSubmit="return OnSubmit(this);">
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
for ($i=0; $i<sizeof($filter_column_array); $i++) {
	echo '
	if (form.'.$filter_column_array[$i].' && (form.'.$filter_column_array[$i].'.value == -1 || form.'.$filter_column_array[$i].'.value == \'\')) {
		form.'.$filter_column_array[$i].'.disabled = true;
	} else {
		form.'.$filter_column_array[$i].'.disabled = false;
	}
';
}
?>
	form.submit();
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
function ChangeURL(url, report_id)
{
	var old_url = document.main_form.action;
	document.main_form.action = url;

	if (report_id != -1) {
		document.main_form.report_id.value = report_id;
	}
	
	DocumentReload();
	document.main_form.action = old_url;
}

//-->
</script>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="index.php"><?php echo $STRING['title_project_list']?></a> / <?php echo htmlspecialchars($project_name)?>
</div>
<div id="main_container">
    <table width="100%" border="0">
		<tr>
			<td align="left" nowrap>
				<img src="images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>">
					<tt class="outline"><?php echo htmlspecialchars($project_name)?></tt>
				</a>
			</td>
			<td nowrap valign="bottom" align="right" width="100%" colspan="2">
				<a href="report_new.php?project_id=<?php echo $_GET['project_id']?>" class="toolbar">
					<img src="images/new_report.png" align="middle" border="0">&nbsp;
					<?php echo $STRING['new_report']?>
				</a>
				&nbsp;
				<a href="index.php"><img src="images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">

		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<table class="table-main-list" align="center">
		<tr>
			<td width="10">&nbsp;</td>
			<td width="100%" align="center" colspan="<?php echo ($column_num+3)?>">
<?php
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");
if ($sort_method == "DESC") {$sort_method = "ASC";} else {$sort_method = "DESC";}

?>

			</td>
			<td width="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="title">&nbsp;</td>
			<td class="title" align="center" nowrap><font size=2><a href="JavaScript:ChangeSort('report_id', '<?php echo $sort_method?>');" style="cursor: pointer"><?php echo $STRING['id']?></a></font></td>
			<td width="40%" class="title" align="center" nowrap><font size=2><a href="JavaScript:ChangeSort('summary', '<?php echo $sort_method?>'); return false;" style="cursor: pointer"><?php echo $STRING['summary']?></a></font></td>
<?php
for ($i = 0; $i < sizeof($column_array); $i++) {
	echo '
			<td class="title" align="center" nowrap>
				<a href="JavaScript:ChangeSort(\''.$column_array[$i].'\', \''.$sort_method.'\');" style="cursor: pointer">
					<font size=2>'.$STRING[$column_array[$i]].'</font>
				</a>
			</td>';
}

if ($sort_method=="DESC") {$sort_method="ASC";} else {$sort_method="DESC";}
?>

			<td width="60" class="title" align="center" nowrap><font size="2"><?php echo $STRING['function']?></font></td>
			<td width="10" class="title" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td class="title" nowrap>&nbsp;</td>
			<td class="title" align="center" nowrap></td>
			<td class="title" align="center"></td>
			<td class="title" align="center"></td>
			<td class="title" align="center"></td>
<?php
for ($i = 0; $i < sizeof($filter_column_array); $i++) {
	echo '
			<td class="title" align="center" nowrap>';

	switch ($filter_column_array[$i]) {
	case "status";
		echo '
				<select name="'.$filter_column_array[$i].'" onChange="document.main_form.'.$filter_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
		for ($j = 1; $j < sizeof($GLOBALS['feedback_status']); $j++) {
			if ($_GET[$filter_column_array[$i]] == $j) {
				$selected = "selected";
			} else {
				$selected = "";
			}
			echo '
					<option value="'.$j.'" '.$selected.'>'.$GLOBALS['feedback_status'][$j]."</option>\n";
		}
		echo '
				</select>'."\n";
		break;
	case "priority":
		echo '
				<select name="'.$filter_column_array[$i].'" onChange="document.main_form.'.$filter_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
		for ($j = sizeof($GLOBALS['priority_array']) - 1; $j > 0; $j--) {
			if ($j == $_GET[$filter_column_array[$i]]) {
				$selected = "selected";
			} else {
				$selected = "";
			}
			echo '
					<option value="'.$j.'" '.$selected.'>'.$STRING[$GLOBALS['priority_array'][$j]]."</option>\n";
		}
		echo '
				</select>'."\n";
		break;
	case "type":
		echo '
				<select name="'.$filter_column_array[$i].'" onChange="document.main_form.'.$filter_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
		for ($j = 1; $j < sizeof($GLOBALS['type_array']); $j++) {
			if ($j == $_GET[$filter_column_array[$i]]) {
				$selected = "selected";
			} else {
				$selected = "";
			}
			echo '
					<option value="'.$j.'" '.$selected.'>'.$STRING[$GLOBALS['type_array'][$j]]."</option>\n";
		}
		echo '
				</select>'."\n";
		break;
	}
	echo '
			</td>';
}
?>

			<td width="60" class="title" align="center" nowrap></td>
			<td width="10" class="title" align="center">&nbsp;</td>
		</tr>
<?php
$num = 0;
while ($row = $allposts->FetchRow()) {
	$report_id = $row["report_id"];
	$cust_report_id = $row["cust_report_id"];
	$summary = $row["summary"];
	$td_class = "line".($num%2);
	$num++;

	echo '
		<tr>
			<td align="center" class="'.$td_class.'" height="40">
				<img border="0" src="images/triangle_s.gif" width="8" height="9">
			</td>
			<td align="center" class="'.$td_class.'">'.$cust_report_id.'</td>
			<td align="left" class="'.$td_class.'">
				<a href="JavaScript:ChangeURL(\'report_show.php\', '.$report_id.');" style="cursor: pointer">
					'.$summary.'
				</a>
			</td>';
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

		} elseif ($column_array[$i] == "created_by") {
			$created_by = $column_value;
			echo "$column_value\n";

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
			<td align="center" width="60" class="'.$td_class.'">';
	
	if (($_SESSION[SESSION_PREFIX.'feedback_customer'] == 0) &&
		($_SESSION[SESSION_PREFIX.'feedback_email'] != $created_by)) {
		// In anonymous mod, user can update their report only.
		echo "&nbsp;";
	} else {
		echo '
				<a href="JavaScript:ChangeURL(\'report_update.php\', '.$report_id.');" style="cursor: pointer">
					'.$STRING['update'].'
				</a>';
	}
	
	echo '
			</td>
			<td width="10" align="center" class="'.$td_class.'">&nbsp;</td>
		</tr>';
}// end of for each report
?>
		<tr>
			<td width="10">&nbsp;</td>
			<td width="100%" align="center" colspan="<?php echo ($column_num+3)?>">
<?php
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");
?>

			</td>
			<td width="10">&nbsp;</td>
		</tr>
		</table>

		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>
<?php
PrintGotoTop();
include("include/tail.php");
?>

