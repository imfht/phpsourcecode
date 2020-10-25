<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: project_list.php,v 1.44 2013/06/30 21:45:28 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/status_function.php");
include("../include/customer_function.php");
include("../include/project_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

function IsBoardRecord($record, $project_id){
	for ($i=0; $i<sizeof($record); $i++) {
		if ($record[$i] == $project_id) {
			return 1;
		}
	}
	return 0;
}

class filter_class {
	var $id;
	var $name;
	function filter_class($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}
}

function GetDefaultFilter()
{
	global $STRING;
	$filters = array();
	
	// System default filter
	$sql = "select * from ".$GLOBALS['BR_filter_table']." where filter_id<0";
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line == 0) {
		return $filters;
	}
	
	while($row = $result->FetchRow()) {
		$filter_id = $row["filter_id"];
		if ($filter_id == -1) {
			$filter_name = $STRING['assigned_to_me'];
		} else if ($filter_id == -2) {
			$filter_name = $STRING['fixed_by_me_last_week'];
		} else {
			$filter_name = $row["filter_name"];
		}
		$filter = new filter_class($filter_id, $filter_name);
		array_push($filters, $filter);
	} // end of while
	return $filters;
}

function GetUserFilter($uid)
{
	$filters = array();
	// User's filter
	$sql = "select * from ".$GLOBALS['BR_filter_table']." where user_id=".$GLOBALS['connection']->QMagic($uid)." order by filter_name";
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line == 0) {
		return $filters;
	}
	
	while($row = $result->FetchRow()) {
		$filter_id = $row["filter_id"];
		$filter_name = $row["filter_name"];

		$filter = new filter_class($filter_id, $filter_name);
		array_push($filters, $filter);
	} // end of while
	return $filters;
}

function GetSharedFilter($userarray, $uid)
{
	$filters = array();
	
	$sql = "select * from ".$GLOBALS['BR_filter_table']." 
		where share_filter='t' and user_id!=".$GLOBALS['connection']->QMagic($uid)." order by user_id,filter_name";
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line == 0) {
		return $filters;
	}
	
	while($row = $result->FetchRow()) {
		$filter_id = $row["filter_id"];
		$filter_name = $row["filter_name"];
		$filter_user_id = $row["user_id"];
		if (IsAccountDisabled($userarray, $filter_user_id)) {
			continue;
		}
		$filter_name = $filter_name.' - '.UidToUsername($userarray, $filter_user_id);
		$filter = new filter_class($filter_id, $filter_name);
		array_push($filters, $filter);
	} // end of if line  
	return $filters;
}

function GetAllLabel2Filter($project_id)
{
	$labels = array();
	$sql = "select * from ".$GLOBALS['BR_label_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->Recordcount();
	if ($line == 0) {
		return $labels;
	}
	
	while($row = $result->FetchRow()) {
		$id = $row["label_id"];
		$name = $row['label_name'];
		$label = new filter_class("label_".$id, $name);
		array_push($labels, $label);
	}
	return $labels;
}

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
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

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	// Check whether user has permission to access this project
	$check_auth_sql = "select * from ".$GLOBALS['BR_proj_access_table']." 
		where user_id='".$_SESSION[SESSION_PREFIX.'uid']."' and project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
	$check_auth_result = $GLOBALS['connection']->Execute($check_auth_sql) or DBError(__FILE__.":".__LINE__);
	$line = $check_auth_result->Recordcount();
	if ($line != 1) {
		WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
		ErrorPrintOut("no_such_xxx", "project");
	} else {
		$lastread = $check_auth_result->UserTimeStamp($check_auth_result->fields["last_read"], "Y-m-d");
		if (!$lastread) {$lastread = "1999-01-01";}
	}
} else {
	$lastread=date("Y-m-d");
}

// Record user has read this project
if (!(IsBoardRecord($_SESSION[SESSION_PREFIX.'board_read'], $_GET['project_id']))){
	array_push($_SESSION[SESSION_PREFIX.'board_read'], $_GET['project_id']);
}

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

$_GET['search_key'] = trim($_GET['search_key']);
if ($_GET['search_key'] == "") {
	unset($_GET['search_key']);
}

/* If search for #num, it means show the report directly.*/
if ( isset($_GET['search_key']) && (preg_match("/^#+[0-9]+[0-9]*$/", $_GET['search_key']))) {
	$search_report_id = substr($_GET['search_key'], 1);
	$get_report_sql = "select * from proj".$_GET['project_id']."_report_table 
			where report_id=".$GLOBALS['connection']->QMagic($search_report_id);
	$get_report_result = $GLOBALS['connection']->Execute($get_report_sql) or DBError(__FILE__.":".__LINE__);
	$get_report_line = $get_report_result->Recordcount();
	if ($get_report_line == 1) {
		echo "<h2 align=\"center\">Report found! Please see <a href=\"report_show.php?project_id=".$_GET['project_id']."&report_id=$search_report_id&choice_filter=".$_GET['choice_filter']."\">here</a></h2>";
		echo "<script>";
		echo "location.href = \"report_show.php?project_id=".$_GET['project_id']."&report_id=$search_report_id\";";
		echo "</script>";
		exit;
	} else {
		$_GET['search_key'] = "";
		ErrorPrintBackFormOut("GET", "project_list.php?project_id=".$_GET['project_id'], $_GET, 
							  "no_such_xxx", "report");
	}
}

/* Get user's setting to decide which fileds to show */
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

/* If thiere is no chosen filter and has default filter, set the chosen filter to default filter. */
if (($_GET['choice_filter'] == "") && ($default_filter != 0) ) {
	$chosen_filter = $default_filter;
} elseif ($_GET['choice_filter'] == "") {
	// Set the chose filter to 0 if no filter
	$chosen_filter= "0";
} else {
	// Has chosen filter
	$chosen_filter = $_GET['choice_filter'];
}

for ($i=0; $i<sizeof($show_column_array); $i++) {
	if (isset($_GET[$show_column_array[$i]])) {
		if ($quick_filter != "") {
			$quick_filter .= " and ";
		}
		$quick_filter .= $show_column_array[$i]."='".$_GET[$show_column_array[$i]]."'";
	}
}

$condition = ConditionByFilterSearch($chosen_filter, $_GET['label'], $_GET['search_key'], $_GET['search_type']);
if ($condition != "") {
	if ($quick_filter != "") {
		$condition = "where (".$condition.") and (".$quick_filter.")";
	} else {
		$condition = "where ".$condition;
	}
} else if ($quick_filter != "") {
	$condition = "where ".$quick_filter;
}

$count_sql="SELECT count(report_id) FROM proj".$_GET['project_id']."_report_table $condition";
$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

$allsql="SELECT * FROM 
		proj".$_GET['project_id']."_report_table
		$condition ORDER BY $sort_by $sort_method";

$allposts = $GLOBALS['connection']->SelectLimit($allsql, $perpage, ($page-1) * $perpage) or DBError(__FILE__.":".__LINE__);
if (($allposts->Recordcount() == 0) && ($page != 1)) {
	$_GET['page'] = 1;
	$page = 1;
	$allposts = $GLOBALS['connection']->SelectLimit($allsql, $perpage, 0) or DBError(__FILE__.":".__LINE__);
}

$userarray = GetAllUsers(1, 1);
$status_array = GetStatusArray();
$customer_array = GetAllCustomers();

// Calculate total fields
$column_num=0;
for ($i=0; $i<sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] == 't') {$column_num++;}
}

?>
<form method="GET" name="main_form" action="<?php echo $_SERVER['PHP_SELF']?>">
	<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
	<input type="hidden" name="search_key" value="<?php echo $_GET['search_key']?>">
	<input type="hidden" name="search_type" value="<?php echo $_GET['search_type']?>">
	<input type="hidden" name="page" value="<?php echo $_GET['page']?>">
	<input type="hidden" name="choice_filter" value="<?php echo $_GET['choice_filter']?>">
	<input type="hidden" name="sort_by" value="<?php echo $_GET['sort_by']?>">
	<input type="hidden" name="sort_method" value="<?php echo $_GET['sort_method']?>">
	<input type="hidden" name="report_id" value="-1">
	<input type="hidden" name="label" value="<?php echo $_GET['label']?>">
<?php
for ($i=0; $i<sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] == 't') {
		echo '
	<input type="hidden" name="'.$show_column_array[$i].'" value="'.$_GET[$show_column_array[$i]].'">';
	}
}
?>
</form>

<div style="display: none;" id="local_search_container">
	<form method="get" name="search_form" action="<?php echo $_SERVER['PHP_SELF']?>" OnSubmit="return OnSubmit(this);">
<?php
PrintTip($STRING['hint_title'], $STRING['search_hint']);
if ($_GET['choice_filter']) {
	echo '<input type="hidden" name="choice_filter" value="'.$_GET['choice_filter'].'">';
}
if ($_GET['label']) {
	echo '<input type="hidden" name="label" value="'.$_GET['label'].'">';
}
$_GET['search_key'] = str_replace('"', "&quot;", $_GET['search_key']);
if (SEARCH_TYPE_AREA == $_GET['search_type']) {
	$type_area_selected = "selected";
}
?>
		<font color="#42649B"><?php echo $STRING['search'].$STRING['colon']?></font>        
		<input class="input-form-text-field" type="text" name="search_key" value="<?php echo stripslashes(rawurldecode($_GET['search_key']))?>" size="25" maxlength="64">
		<select name="search_type" size="1">
			<option value="<?php echo SEARCH_TYPE_CONTENT;?>"><?php echo $STRING['subject_and_content'];?></option>
			<option value="<?php echo SEARCH_TYPE_AREA;?>" <?php echo $type_area_selected;?>><?php echo $STRING['area'];?></option>
		</select>
		<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
		<input type="submit" class="button" value="<?php echo $STRING['button_go']?>" name="B1">
	</form>
</div>

<script language="JavaScript" src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/javascript/label.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
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
		form.search_type.disabled = true;
	} else {
		form.search_key.disabled = false;
		form.search_type.disabled = false;
	}

	if (form.choice_filter.value == '') {
		form.choice_filter.disabled = true;
	} else {
		form.choice_filter.disabled = false;
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
	if (form.label.value == '') {
		form.label.disabled = true;
	} else {
		form.label.disabled = false;
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
for ($i=0; $i<sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] != 't') {
		continue;
	}
	echo '
	if (form.'.$show_column_array[$i].' && (form.'.$show_column_array[$i].'.value == -1 || form.'.$show_column_array[$i].'.value == \'\')) {
		form.'.$show_column_array[$i].'.disabled = true;
	} else {
		form.'.$show_column_array[$i].'.disabled = false;
	}
';
}
?>
	form.submit();
}
function ChangeFilter()
{
	var filter_selector = document.getElementById('filter_selector');
	if (filter_selector.options[filter_selector.selectedIndex].value == 'setting') {
		parent.location = '../user/filter_setting.php?project_id=<?php echo $_GET['project_id']?>';
		return;
	}
	document.main_form.choice_filter.value = filter_selector.options[filter_selector.selectedIndex].value;
	document.main_form.page.value = -1;
	DocumentReload();
}
function ChangeSort(sort_by, sort_method)
{
	document.main_form.sort_by.value = sort_by;
	document.main_form.sort_method.value = sort_method;
	document.main_form.page.value = -1;
	DocumentReload();
	return;
}
function ChangePage(page)
{
	document.main_form.page.value = page;
	DocumentReload();
	return;
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
	return;
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
				ChangeURL('report_delete.php', id, false);
			}
			return;
		}
	});
	return;
}
function LocalLabelActionHandler(dropdown)
{
	var item = dropdown.options[dropdown.selectedIndex];
	var LabelIDReg = /^label_[\-0-9]/;

	if (!LabelIDReg.test(item.value)) {
		// Not search event
		return LabelActionHandler(dropdown);
	}

	var LabelID = item.value.substr(6);
	document.main_form.label.value = LabelID;
	document.main_form.page.value = -1;
	DocumentReload();
	
	return;
}
//-->
</script>

<div id="current_location" class="project_list">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b>
	/<a href="../index.php"><?php echo $STRING['title_project_list']?></a>/<?php echo htmlspecialchars($project_name)?>
</div>
<div id="main_container" class="project_list">
	<table width="100%" border="0">
		<tr>
			<td  align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_project.png" width="48" height="48" align="middle" border="0">
				<a href="project_list.php?project_id=<?php echo $_GET['project_id']?>"><tt class="outline"><?php echo htmlspecialchars($project_name)?></tt></a>
			</td>
			<td nowrap width="100%" valign="bottom" align="center">
				<font color="#42649B"><?php echo $STRING['filter'].$STRING['colon']?></font>
				<select size="1" id="filter_selector" onChange="ChangeFilter();">
<?php
$filters = array(new filter_class("setting", $STRING['set_filter']), new filter_class(0, $STRING['none']));
$defaultfilters = GetDefaultFilter();
$filters = array_merge($filters, $defaultfilters);

$userfilters = GetUserFilter($_SESSION[SESSION_PREFIX.'uid']);
$filters = array_merge($filters, $userfilters);

if ($setting_row["show_shared_filter"] == 't') {
	$sharedfilters = GetSharedFilter($userarray, $_SESSION[SESSION_PREFIX.'uid']);
	$filters = array_merge($filters, $sharedfilters);
}

for ($i = 0; $i < sizeof($filters); $i++) {
	if ($filters[$i]->id == $chosen_filter) {
		echo '
					<option value="'.$filters[$i]->id.'" selected>'.$filters[$i]->name.'</option>';
	}else{
		echo '
					<option value="'.$filters[$i]->id.'">'.$filters[$i]->name.'</option>';
	}
}

?>
				</select>

				<font color="#42649B"><?php echo $STRING['label'].$STRING['colon']?></font>
				<select size="1" id="label_selector" onChange="LocalLabelActionHandler(this);">
					<option value="action" style="color: rgb(119, 119, 119);"><?php echo $STRING['label_actions'];?></option>
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_manage_label']) {
?>						
					<option value="manage"><?php echo $STRING['label_management']?></option>						
<?php
}
?>						
					<optgroup id="label_selector_searchgroup" label="<?php echo $STRING['search_label'];?>">
<?php
$labels = GetAllLabel2Filter($_GET['project_id']);
for ($i = 0; $i < sizeof($labels); $i++) {
	$id = substr($labels[$i]->id, 6); // 6: label_
	if ($id == $_GET['label']) {
		$selected = "-&gt;";
	} else {
		$selected = "";
	}
	echo '
						<option value="'.$labels[$i]->id.'">'.$selected.$labels[$i]->name.'</option>';
}
?>
					</optgroup>
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_manage_label']) {
?>						
					<optgroup id="label_selector_applygroup" label="<?php echo $STRING['apply_label'];?>">
						<option value="new" style="color: rgb(255, 0, 0)"><?php echo $STRING['new_label']?>...</option>
	<?php
	for ($i = 0; $i < sizeof($labels); $i++) {
		$id = substr($labels[$i]->id, 6); // 6: label_
		echo '
						<option value="'.$id.'">'.$labels[$i]->name.'</option>';
	}
	?>
					</optgroup>
					<optgroup id="label_selector_removegroup" label="<?php echo $STRING['remove_label'];?>" style="display:none;"></optgroup>
<?php
}
?>
				</select>
			</td>
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_create_report']) {
	echo '
			<td nowrap valign="bottom">
				<a href="report_new.php?project_id='.$_GET['project_id'].'">
					<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/new_report.png" align="middle" border="0">&nbsp;
					'.$STRING['new_report'].'
				</a>&nbsp;
			</td>';
}
?>
			<td nowrap valign="bottom">
				<a href="JavaScript:ChangeURL('project_export.php', -1, false);">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/export.png" border="0" align="middle"><?php echo $STRING['export']?></a>
			</td>
			<td nowrap valign="bottom">
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
			<td width="100%" align="center" colspan="<?php echo $column_num+3?>">
<?php
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");

if ($sort_method == "DESC") {$sort_method = "ASC";} else {$sort_method = "DESC";}
?>
			</td>
			<td width="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="title" nowrap>&nbsp;</td>
			<td width="30" class="title" align="center" nowrap><font size=2><a href="JavaScript:ChangeSort('report_id', '<?php echo $sort_method?>');"><?php echo $STRING['id']?></a></font></td>
			<td width="40%" class="title" align="center"><font size=2><a href="JavaScript:ChangeSort('summary', '<?php echo $sort_method?>');"><?php echo $STRING['summary']?></a></font></td>
<?php
for ($i = 0; $i < sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] == 't') {
		echo '
			<td class="title" align="center" nowrap>
				<a href="JavaScript:ChangeSort(\''.$show_column_array[$i].'\', \''.$sort_method.'\');">
				'.$STRING[$show_column_array[$i]].'</a>
			</td>';
	}
}
?>

			<td width="60" class="title" align="center" nowrap><?php echo $STRING['function']?></td>
			<td width="10" class="title" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="title" nowrap>
<?php
if ($GLOBALS['Privilege'] & $GLOBALS['can_manage_label']) {
	echo '
				<input type="checkbox" id="checkboxall" class="checkbox">';
}
?>
			</td>
			<td width="30" class="title" align="center" nowrap></td>
			<td width="40%" class="title" align="center"></td>
<?php
for ($i = 0; $i < sizeof($show_column_array); $i++) {
	$show_column = "show_".$show_column_array[$i];
	if ($setting_row[$show_column] == 't') {
		echo '
			<td class="title" align="center" nowrap>';
		

		switch ($show_column_array[$i]) {
		case "reported_by":
		case "assign_to":
		case "fixed_by":
		case "verified_by":
			echo '
				<select name="'.$show_column_array[$i].'" onChange="document.main_form.'.$show_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
			for ($j = 0; $j < sizeof($userarray); $j++) {
				if ($userarray[$j]->getdisabled()) {
					continue;
				}
				if ($_GET[$show_column_array[$i]] == $userarray[$j]->getuserid()) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				echo '
					<option value="'.$userarray[$j]->getuserid().'" '.$selected.'>'.$userarray[$j]->getusername().'</option>';
				
			}
			echo '
				</select>'."\n";
			break;
		case "status";
			echo '
				<select name="'.$show_column_array[$i].'" onChange="document.main_form.'.$show_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
			for ($j = 0; $j < sizeof($status_array); $j++) {
				if ($_GET[$show_column_array[$i]] == $status_array[$j]->getstatusid()) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				echo '
					<option value="'.$status_array[$j]->getstatusid().'" '.$selected.'>'.$status_array[$j]->getstatusname().'</option>';
			}
			echo '
				</select>'."\n";
			break;
		case "area":
			$all_area = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and area_parent=0 order by area_name";
			$root_area_result = $GLOBALS['connection']->Execute($all_area) or DBError(__FILE__.":".__LINE__);
			$line = $root_area_result->Recordcount();
			if ($line != 0) {
				echo '
				<select name="'.$show_column_array[$i].'" onChange="document.main_form.'.$show_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
				while ($root_area_row = $root_area_result->FetchRow()) {
					$area_name = $root_area_row["area_name"];

					if (stripslashes($_GET[$show_column_array[$i]]) == $area_name) {
						$selected = "selected";
					} else {
						$selected = "";
					}
					
					echo '
					<option value="'.$area_name.'" '.$selected.'>'.$area_name.'</option>';
				}
				echo '
				</select>'."\n";
			}
			break;
		case "minor_area":
			if (isset($_GET['filter_area']) && $_GET['filter_area'] != -1) {
				$all_minor_area = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and area_parent=".$GLOBALS['connection']->QMagic($_GET['filter_area'])." order by area_name";
				$minor_area_result = $GLOBALS['connection']->Execute($all_minor_area) or DBError(__FILE__.":".__LINE__);
				$line = $minor_area_result->Recordcount();
				if ($line != 0) {
					echo '
				<select name="'.$show_column_array[$i].'" onChange="document.main_form.'.$show_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
					while ($minor_area_row = $minor_area_result->FetchRow()) {
						$minor_area_name = $minor_area_row["area_name"];
						if (stripslahses($_GET[$show_column_array[$i]]) == $minor_area_name) {
							$selected = "selected";
						} else {
							$selected = "";
						}
						echo '
					<option value="'.$minor_area_name.'" '.$selected.'>'.$minor_area_name.'</option>';
					}
					echo '
				</select>'."\n";
				}
			}
			break;
		case "priority":
			echo '
				<select name="'.$show_column_array[$i].'" onChange="document.main_form.'.$show_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
			for ($j = sizeof($GLOBALS['priority_array']) - 1; $j > 0; $j--) {
				if ($j == $_GET[$show_column_array[$i]]) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				echo '
					<option value="'.$j.'" '.$selected.'>'.$STRING[$GLOBALS['priority_array'][$j]].'</option>';
			}
			echo '
				</select>'."\n";
			break;
		case "type":
			echo '
				<select name="'.$show_column_array[$i].'" onChange="document.main_form.'.$show_column_array[$i].'.value=this.options[this.selectedIndex].value; DocumentReload();">
					<option value=-1></option>';
			for ($j = 1; $j < sizeof($GLOBALS['type_array']); $j++) {
				if ($j == $_GET[$show_column_array[$i]]) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				echo '
					<option value="'.$j.'" '.$selected.'>'.$STRING[$GLOBALS['type_array'][$j]].'</option>';
			}
			echo '
				</select>'."\n";
			break;
		}
		echo '
			</td>';
	}
}
?>

			<td width="60" class="title" align="center" nowrap></td>
			<td width="10" class="title" align="center">&nbsp;</td>
		</tr>
<?php
if ($sort_method=="DESC") {$sort_method="ASC";} else {$sort_method="DESC";}

// �H�j�����ܩҦ� report
$show_in_blank="false";
if ($setting_row["show_in_blank"] == 't') {
	$show_in_blank="true";
}

$num = 0;
$ReportIDList = array();
while ($row = $allposts->FetchRow()) {
	$report_id = $row["report_id"];
	$summary = $row["summary"];
	$last_update = $row["last_update"];
	$last_update = substr($last_update, 0, 10);
	$td_class = "line".($num%2);
	$num++;

	array_push($ReportIDList, $report_id);

	echo '
		<tr>
			<td align="center" class="'.$td_class.'">';
	if ($GLOBALS['Privilege'] & $GLOBALS['can_manage_label']) {
		echo '
				<input type="checkbox" id="checkbox'.$report_id.'" value="y">';
	} else {
		echo '
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">';
	}
	echo '
			</td>
			<td align="center" class="'.$td_class.'">'.$report_id.'</td>
			<td align="left" class="'.$td_class.'">
				<div id="main_subject_container'.$report_id.'">';
	PrintLabel($_GET['project_id'], $report_id);
	echo '
					<div id="subject_'.$report_id.'">
						<a href="JavaScript:ChangeURL(\'report_show.php\', '.$report_id.', '.$show_in_blank.');">
							'.$summary.'
						</a>
					</div>
				</div>';

	$status = GetStatusClassByID($status_array, $row['status']);
	if ($status) {
		$status_type = $status->getstatustype();
	} else {
		$status_type = "active";
	}

	if ($last_update>=$lastread && $status_type == "active") {
		echo '
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/new.gif" width="20" height="10">';
	}
	echo '
			</td>';

	for ($i = 0; $i < sizeof($show_column_array); $i++) {
		$show_column = "show_".$show_column_array[$i];
		if ($setting_row[$show_column] == 't') {
			$column_value = $row[$show_column_array[$i]];

			echo '
			<td class="'.$td_class.'" align="center">';
			if ($show_column_array[$i] == "priority") {

				$priority = $STRING[$GLOBALS['priority_array'][$column_value]];
				echo '<font color='.$GLOBALS['priority_color'][$column_value].'>'.$priority.'</font>';

			} elseif ($show_column_array[$i] == "type") {

				echo $STRING[$GLOBALS['type_array'][$column_value]];

			} elseif ($show_column_array[$i] == "status") {

				$status = GetStatusClassByID($status_array, $column_value);
				if ($status) {
					echo '<font color="'.$status->getstatuscolor().'">'.$status->getstatusname().'</font>';
				}

			} elseif ($show_column_array[$i] == "reported_by_customer") {

				echo GetCustomerNameFromID($customer_array, $column_value)." \n";

			} elseif (($show_column_array[$i] == "reported_by") || ($show_column_array[$i] == "assign_to") || 
					  ($show_column_array[$i] == "fixed_by") || ($show_column_array[$i] == "verified_by") ) {

				if (($column_value == 0) && ($show_column_array[$i] != "reported_by")){
					echo "&nbsp;";
				} else {
					if ($column_value == $_SESSION[SESSION_PREFIX.'uid']) {
						echo '<font color="#FF7F50">'.UidToUsername($userarray, $column_value).'</font>';
					} else {
						echo UidToUsername($userarray, $column_value)." \n";
					}
				}

			} elseif (($show_column_array[$i] == "created_date") || ($show_column_array[$i] == "fixed_date") ||
					  ($show_column_array[$i] == "verified_date")|| ($show_column_array[$i] == "estimated_time")) {

				if ($column_value != "") {
					$column_value = $allposts->UserTimeStamp($column_value, GetDateFormat());
				}
				
				echo $column_value;

			} else {

				echo $column_value;

			}
			echo '
			</td>';
		} /* end of show column */
	} /* for each column */

	echo '
			<td align="center" width="60" class="'.$td_class.'">';
	if ($GLOBALS['Privilege'] & $GLOBALS['can_update_report']) {
		echo '
				<a href="JavaScript:ChangeURL(\'report_update.php\', '.$report_id.', false);">
					'.$STRING['update'].'
				</a><br>';
	} else {
		echo $STRING['update']."<br> \n";
	}
	if ($GLOBALS['Privilege'] & $GLOBALS['can_delete_report']) {
		echo '<a href="JavaScript:ConfirmDelete('.$report_id.');">';
		echo $STRING['delete']."</a> \n";
	} else {
		echo $STRING['delete']." \n";
	}
	
	echo '
			</td>
			<td width="10" align="center" class="'.$td_class.'">&nbsp;</td>
		</tr>';
}// end of for each report

echo '
		<tr>
			<td width="10" valign="bottom">&nbsp;</td>
            <td width="100%" align="center" colspan="'.($column_num+3).'">';
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");

echo '
			</td>
			<td width="10" valign="bottom">&nbsp;</td>
		</tr>';
?>
		</table>

		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>


<script language="JavaScript" type="text/javascript">
ALEXWANG.LabelHandler.Init({
	project_id: <?php echo $_GET['project_id']?>,
	bugids: [<?php echo implode(",", $ReportIDList)?>],
	checkbox_prefix: 'checkbox',
	container_prefix: 'main_subject_container',
	label_color: <?php PrintLabelColorArray($_GET['project_id'])?>,
	label_selector: 'label_selector'
});
</script>
<?php
PrintGotoTop();

include("../include/tail.php");
?>

