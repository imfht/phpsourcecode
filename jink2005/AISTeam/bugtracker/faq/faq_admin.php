<?php 
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_admin.php,v 1.18 2013/07/07 21:31:39 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_faq'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
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

$extra_params = GetExtraParams($_GET, "search_key,faq_class, page");

?>
<div style="display: none;" id="local_search_container">
	<form method="get" name="search_form" action="<?php echo $_SERVER['PHP_SELF']?>" OnSubmit="return OnSubmit(this);">
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

function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['faq'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'faq_delete.php?faq_id='+id+'<?php echo $extra_params?>';
				}
				return;
			}
	});
	return;
}
function ChangeClass()
{
	
	document.form1.submit();
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b>
	/ <a href="../index.php"><?php echo $STRING['title_project_list']?></a> / <?php echo htmlspecialchars($project_name)?> / 
	<?php echo $STRING['faq']?>
</div>
<div id="main_container">
	
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_faq.png" width="48" height="48" align="middle" border="0">
				<a href="faq_admin.php?project_id=<?php echo $_GET['project_id']?>"><tt class="outline"><?php echo $STRING['faq']?></tt></a></td>
			<td nowrap width="100%" align="center" valign="bottom">
                <form method="GET" name="form1" action="<?php echo $_SERVER['PHP_SELF']?>">
					<input type="hidden" name="project_id" value="<?php echo $_GET['project_id']?>">
					<input type="hidden" name="search_key" value="<?php echo stripslashes($_GET['search_key'])?>">
				
<?php 

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

$condition = "where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id']);
$_GET['search_key'] = trim($_GET['search_key']);
if ($_GET['search_key'] != ""){
	$condition .= " and (question ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%")." 
		or answer ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%").")";
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
    $count = $count_result->fields[0];

	if ($all_class_id[$i] == $_GET['faq_class']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '<option value="'.$all_class_id[$i].'" '.$selected.'>'.$all_class_name[$i].'('.$count.")</option> \n";
}
echo '</select>';
?>
				</form>
			</td>
			<td nowrap valign="bottom">
				<a href="faq_new.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_faq.png" border="0" align="middle"><?php echo $STRING['new_faq']?></a>
			</td>
			<td nowrap valign="bottom">
				<a href="faq_class.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/faq_class.png" border="0" align="middle"><?php echo $STRING['faq_class']?></a>
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
				<td colspan="5" align="center">
<?php 
$perpage_sql = "select perpage from ".$GLOBALS['BR_user_table']." where user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$perpage_result = $GLOBALS['connection']->Execute($perpage_sql) or DBError(__FILE__.":".__LINE__);
$perpage = $perpage_result->fields["perpage"];

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

PrintPageLink($count, $page, $perpage, $_SERVER['PHP_SELF'], "project_id=".$_GET['project_id'].$extra_params);

?>
				</td>
			</tr>
			<tr>
				<td class="title" width="10">&nbsp;</td>
				<td align="center" width="40" class="title"><?php echo $STRING['id']?></td>
				<td align="center" width="450" class="title"><?php echo $STRING['question']?></td>
				<td align="center" width="90" class="title"><?php echo $STRING['function']?></td>
				<td class="title" width="10">&nbsp;</td>
			</tr>
<?php 
$sql = "select * from ".$GLOBALS['BR_faq_content_table']." $condition order by faq_id ASC";
$result = $GLOBALS['connection']->SelectLimit($sql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);

$count_row=0;
while ($row = $result->FetchRow()) {
	$id = $row["faq_id"];
	$question = $row["question"];
	$td_class="line".($count_row%2);

	echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS['SYS_URL_ROOT'].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td align="center" class="'.$td_class.'">'.$id.'</td>
				<td align="left" class="'.$td_class.'">
					<a href="faq_show.php?faq_id='.$id.$extra_params.'&page='.$page.'">'.$question.'</a>
				</td>
				<td align="center" class="'.$td_class.'">
					<a href="faq_edit.php?faq_id='.$id.$extra_params.'&page='.$page.'">
					'.$STRING['edit'].'</a>&nbsp;&nbsp;
					<a href="JavaScript:ConfirmDelete('.$id.');">'.$STRING['delete'].'</a>
				</td>
				<td class="'.$td_class.'">&nbsp;</td>
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
include("../include/tail.php");
?>
