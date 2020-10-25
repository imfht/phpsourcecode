<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq.php,v 1.15 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/project_function.php");
include("include/datetime_function.php");

AuthCheckAndLogin();

if (isset($_GET['project_id']) && CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$project_array = GetAllProjects();

if (!isset($_GET['project_id'])) {
	for ($i = 0; $i < sizeof($project_array); $i++) {
		$this_id = $project_array[$i]->getprojectid();
	
		if (CheckProjectAccessable($this_id, $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
			continue;
		}
		$_GET['project_id'] = $this_id;
		break;
	}
}
	
$extra_params = GetExtraParams($_GET, "search_key,faq_class");
?>
<div style="display: none;" id="local_search_container">
	<form method="get" name="search_form" action="<?php echo $_SERVER['PHP_SELF']?>" onSubmit="return OnSubmit(this);">
<?php
PrintTip($STRING['hint_title'], $STRING['faq_search_hint']);
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

function ChangeProject() {
	document.form1.faq_class.selectedIndex = 0;
	document.form1.submit();
}
function ChangeClass() {
	
	document.form1.submit();
}
//-->
</script>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> / <?php echo $STRING['faq']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="images/outline_faq.png" width="48" height="48" align="middle" border="0">
				<a href="faq.php"><tt class="outline"><?php echo $STRING['faq']?></tt></a>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
                <form method="GET" name="form1" action="<?php echo $_SERVER['PHP_SELF']?>">
					<input type="hidden" name="search_key" value="<?php echo stripslashes($_GET['search_key'])?>">
				
<?php
echo $STRING['project_name'].$STRING['colon'];
echo '<select name="project_id" size="1" onchange="ChangeProject();">';

if ($_GET['search_key'] != "") {
	$condition = " and (question ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%")." or 
			answer ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%").")";
}

for ($i = 0; $i < sizeof($project_array); $i++) {
	$this_id = $project_array[$i]->getprojectid();

	if (CheckProjectAccessable($this_id, $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
		continue;
	}
	$count_sql = "select count(*) from ".$GLOBALS['BR_faq_content_table']." where project_id=".$GLOBALS['connection']->QMagic($this_id)." and 
		is_verified='t' $condition";
	$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
    $count_faq = $count_result->fields[0];

	if ($this_id == $_GET['project_id']) {
		echo "<option value=".$this_id." selected>".htmlspecialchars($project_array[$i]->getprojectname())." ($count_faq)</option>\n";
	} else {
		echo "<option value=".$this_id.">".htmlspecialchars($project_array[$i]->getprojectname())." ($count_faq)</option>\n";
	}
}
echo "</select>";
echo "&nbsp;&nbsp;";

$get_class_sql = "select * from ".$GLOBALS['BR_faq_class_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$get_class_result = $GLOBALS['connection']->Execute($get_class_sql) or DBError(__FILE__.":".__LINE__);
$all_class_id = array();
$all_class_name = array();
while ($row = $get_class_result->FetchRow()) {
	$faq_class_id = $row["faq_class_id"];
	$class_name = $row["class_name"];
	array_push($all_class_id, $faq_class_id);
	array_push($all_class_name, $class_name);
}

$condition = "where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and is_verified='t' ";
$_GET['search_key'] = trim($_GET['search_key']);
if ($_GET['search_key'] != "") {
	$condition .= " and (question ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%")." or 
		answer ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%").")";
}

echo $STRING['class_name'].$STRING['colon'];
echo '<select name="faq_class" size="1" onchange="ChangeClass();">';
echo '<option value="-1">'.$STRING['all_classes'].'</option>';
for ($i = 0; $i < sizeof($all_class_id); $i++) {
	if ($_GET['search_key'] != "") {
		$count_sql = "select count(*) from ".$GLOBALS['BR_faq_map_table']." where faq_class_id=".$GLOBALS['connection']->QMagic($all_class_id[$i])." and
			faq_id in (select faq_id from ".$GLOBALS['BR_faq_content_table']." $condition)";
	} else {
		$count_sql = "select count(*) from ".$GLOBALS['BR_faq_map_table']." where faq_class_id=".$GLOBALS['connection']->QMagic($all_class_id[$i]);
	}
	
	$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
    $count_class = $count_result->fields[0];

	if ($all_class_id[$i] == $_GET['faq_class']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '<option value="'.$all_class_id[$i].'" '.$selected.'>'.$all_class_name[$i]."($count_class)</option> \n";
}
echo '</select>';
?>
				</form>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">

		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<table class="table-main-list" align="center">
		<tr>
			<td colspan="4" align="center">
<?php
$perpage = ITEMS_PER_PAGE;

if (($_GET['faq_class'] != "") && ($_GET['faq_class'] != -1)) {
	$condition .= " and (faq_id in (select faq_id from ".$GLOBALS['BR_faq_map_table']." where faq_class_id=".$GLOBALS['connection']->QMagic($_GET['faq_class'])."))";
}

$count_sql = "select count(*) from ".$GLOBALS['BR_faq_content_table']." $condition";
$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];
if (!$_GET['page']) {
	$page = 1;
} else {
	$page = $_GET['page'];
}

$startat = ($page-1) * $perpage;
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");
?>
			</td>
		</tr>

		<tr>
			<td class="title" width="10">&nbsp;</td>
			<td align="center" width="480" class="title"><?php echo $STRING['question']?></td>
			<td align="center" width="100" class="title"><?php echo $STRING['last_update']?></td>
			<td class="title" width="10">&nbsp;</td>
		</tr>
<?php

$sql = "select * from ".$GLOBALS['BR_faq_content_table']." $condition order by last_update DESC";
$result = $GLOBALS['connection']->SelectLimit($sql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);;
$count_row=0;
while ($row = $result->FetchRow()) {
	$id = $row["faq_id"];
	$question = $row["question"];
	$last_update = $result->UserTimeStamp($row["last_update"], GetDateFormat());
	$td_class="line".($count_row%2);
     
	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="images/triangle_s.gif" width="8" height="9">
				</td>
				<td align="left" class="'.$td_class.'">
					<a href="faq_show.php?faq_id='.$id.'&project_id='.$_GET['project_id'].$extra_params.'&page='.$page.'">
						'.$question.'
					</a>
				</td>
				<td align="center" class="'.$td_class.'">'.$last_update.'</td>
				<td class="'.$td_class.'">&nbsp</td>
			</tr>';
	$count_row++;
}
?>
			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>


<?php
include("include/tail.php");
?>