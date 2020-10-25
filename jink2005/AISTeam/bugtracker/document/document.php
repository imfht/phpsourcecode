<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document.php,v 1.27 2013/07/07 21:27:40 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_document'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_GET['search_key'] != "") {
	$subject_match = "(subject ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%").")";
	$desc_match = "(description ".PATTERN_KEYWORD." ".$GLOBALS['connection']->QMagic("%".$_GET['search_key']."%").")";
	if (strstr($_GET['search_key'], " and ")) {   
		$subject_match = str_replace(" and ","%' and subject ".PATTERN_KEYWORD." '%",$subject_match);
		$desc_match = str_replace(" and ","%' and description ".PATTERN_KEYWORD." '%",$desc_match);
	}
	if (strstr($_GET['search_key'], " not ")) {
		$subject_match = str_replace(" not ","%' and subject not ".PATTERN_KEYWORD." '%",$subject_match);
		$desc_match = str_replace(" not ","%' and description not ".PATTERN_KEYWORD." '%",$desc_match);
	}
	if (strstr($_GET['search_key']," or ")) {
		$subject_match = str_replace(" or ","%' or subject ".PATTERN_KEYWORD." '%",$subject_match);
		$desc_match = str_replace(" or ","%' or description ".PATTERN_KEYWORD." '%",$desc_match);
	}
	$condition = "(".$subject_match." or ".$desc_match.")";
}

if (isset($_GET['document_class']) && ($_GET['document_class'] != "-1") && ($_GET['document_class'] != "")) {
	if ($condition == "") {
		$condition = "(document_id in (select document_id from ".$GLOBALS['BR_document_map_table']." where document_class_id='".$_GET['document_class']."'))";
	} else {
		$condition .= " and (document_id in (select document_id from ".$GLOBALS['BR_document_map_table']." where document_class_id='".$_GET['document_class']."'))";
	}
}

if (!isset($_GET['group_class']) || ($_GET['group_class'] == "-1") || ($_GET['group_class'] == "")) {
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		$count_sql = "SELECT count(document_id) FROM ".$GLOBALS['BR_document_table'];
		if ($condition != "") {
			$count_sql .= " where ".$condition;
		}
	} else {
		$count_sql = "SELECT count(document_id) FROM ".$GLOBALS['BR_document_table']." 
			where (allow_other_group='t'
			or created_by = '".$_SESSION[SESSION_PREFIX.'uid']."')";
		if ($condition != "") {
			$count_sql .= " and ".$condition;
		}
	}
} else if ($_GET['group_class'] == $_SESSION[SESSION_PREFIX.'gid']) {
	$count_sql = "SELECT count(document_id) FROM ".$GLOBALS['BR_document_table']." 
	where group_class='".$_GET['group_class']."'";
	if ($condition != "") {
		$count_sql .= " and ".$condition;
	}
} else {
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		$count_sql = "SELECT count(document_id) FROM ".$GLOBALS['BR_document_table']." 
			where group_class='".$_GET['group_class']."'";
		if ($condition != "") {
			$count_sql .= " and ".$condition;
		}
	} else {
		$count_sql = "SELECT count(document_id) FROM ".$GLOBALS['BR_document_table']." 
			where (group_class=".$GLOBALS['connection']->QMagic($_GET['group_class'])." and 
			(allow_other_group='t' or created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid'])."))";
		if ($condition != "") {
			$count_sql .= " and ".$condition;
		}
	}
}
$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

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


?>

<div style="display: none;" id="local_search_container">
	<form method="get" name="search_form" action="<?php echo $_SERVER['PHP_SELF']?>" OnSubmit="return OnSubmit(this);">
<?php
PrintTip($STRING['hint_title'], $STRING['document_search_hint']);
if ($_GET['group_class'] != "") {
	echo '<input type="hidden" name="group_class" value="'.$_GET['group_class'].'">';
}
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

function ChangeCondition() 
{
	form = document.form1;
<?php
	if ($_GET['search_key'] == "") {
		echo 'form.search_key.disabled = true;';
	}
?>
	if (form.group_class.options[form.group_class.selectedIndex].value == -1) {
		form.group_class.disabled = true;
	} else {
		form.group_class.disabled = false;
	}
	if (form.document_class.options[form.document_class.selectedIndex].value == -1) {
		form.document_class.disabled = true;
	} else {
		form.document_class.disabled = false;
	}
	if (form.page.value == -1) {
		form.page.disabled = true;
	} else {
		form.page.disabled = false;
	}
	if (form.document_id.value == -1) {
		form.document_id.disabled = true;
	} else {
		form.document_id.disabled = false;
	}
	form.submit();
}

function ChangePage(page)
{
	document.form1.page.value = page;
	ChangeCondition();
}

function RedirectPage(action, document_id)
{
	document.form1.action = action;
	document.form1.document_id.value = document_id;
	ChangeCondition();
}
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['document'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					RedirectPage('document_delete.php', id);
				}
				return;
			}
	});
	return;
}
//-->
</script>
<div id="main_container">
	<form method="GET" action="<?php echo $_SERVER['PHP_SELF']?>" name="form1">
		<input type="hidden" name="search_key" value="<?php echo stripslashes($_GET['search_key'])?>">
		<input type="hidden" name="page" value="-1">
		<input type="hidden" name="document_id" value="-1">
		<table width="100%" border="0">
		<tr>
			<td align="center" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_document.png" width="48" height="48" align="middle" border="0">
				<a href="<?php echo $_SERVER['PHP_SELF']?>"><tt class="outline"><?php echo $STRING['title_document']?></tt></a>
			</td>
			<td width="60%" align="center" nowrap>
				<font color="#42649b"><?php echo $STRING['show_doc_for_group'].$STRING['colon']?></font>
				<select size="1" name="group_class" onChange="ChangeCondition();">
				<option value="-1"><?php echo $STRING['all_groups']?></option>
<?php
$group_array = GetAllGroups();
for ($i = 0; $i < sizeof($group_array); $i++) {
	$group_id = $group_array[$i]->getgroupid();
	$group_name = $group_array[$i]->getgroupname();

	if ($_GET['group_class'] == $group_id) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
				<option value="'.$group_id.'" '.$selected.'>'.$group_name.'</option>';
}
?>

				</select>
			</td>
			<td width="100%" align="left" nowrap>
				<font color="#42649b"><?php echo $STRING['class_name'].$STRING['colon']?></font>
				<select size="1" name="document_class" onChange="ChangeCondition();">
				<option value="-1"><?php echo $STRING['all_classes']?></option>
<?php
$get_class_sql = "select * from ".$GLOBALS['BR_document_class_table']." order by class_name ASC";
$get_class_result = $GLOBALS['connection']->Execute($get_class_sql) or DBError(__FILE__.":".__LINE__);
$all_class_id = array();
$all_class_name = array();
while ($row = $get_class_result->FetchRow()) {
	$document_class_id = $row["document_class_id"];
	$class_name = $row["class_name"];
	array_push($all_class_id, $document_class_id);
	array_push($all_class_name, $class_name);
}
for ($i = 0; $i < sizeof($all_class_id); $i++) {

	if ($all_class_id[$i] == $_GET['document_class']) {
		$selected = "selected";
	} else {
		$selected = "";
	}
	echo '
				<option value="'.$all_class_id[$i].'" '.$selected.'>'.$all_class_name[$i].'</option>';
}
?>

				</select>
			</td>
<?php
if ( $GLOBALS['Privilege'] & $GLOBALS['can_create_document']) {
	echo '
			<td nowrap valign="bottom">
				<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/new_document.png" border="0" align="middle">
				<a href="document_new.php">'.$STRING['new_document'].'</a>
			</td>';
}
if ( $GLOBALS['Privilege'] & $GLOBALS['can_manage_document_class']) {
	echo '
			<td nowrap valign="bottom">
				<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/document_class.png" border="0" align="middle">
				<a href="document_class.php">'.$STRING['document_class'].'</a>
			</td>';
}
?>

			</tr>
		</table>
	</form>

	<div id="sub_container" style="width: 98%;">
	
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<table class="table-main-list" align="center">
		<tr>
			<td width="100%" colspan="7" align="center">
<?php
PrintPageLink($count, $page, $perpage, "", "", "ChangePage");
?>

			</td>
		</tr>
		<tr>
			<td class="title" width="10">&nbsp;</td>
			<td class="title" width="300" align="center"><?php echo $STRING['subject']?></td>
			<td class="title" width="100" align="center"><?php echo $STRING['created_by']?></td>
			<td class="title" width="100" align="center"><?php echo $STRING['last_update']?></td>
			<td class="title" align="center" width="80"><?php echo $STRING['file']?></td>
			<td class="title" align="center" width="100"><?php echo $STRING['function']?></td>
			<td class="title" width="10">&nbsp;</td>
		</tr>
	
<?php

if (!isset($_GET['group_class']) || ($_GET['group_class'] == "-1") || ($_GET['group_class'] == "")) {
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		$sql = "select document_id,subject,created_by,last_update,filename from ".$GLOBALS['BR_document_table'];
		if ($condition != "") {
			$sql .= " where $condition ";
		}
		$sql .= " order by last_update DESC";
	} else {
		$sql = "select document_id,subject,created_by,last_update,filename from ".$GLOBALS['BR_document_table']."
			where (allow_other_group='t' or created_by='".$_SESSION[SESSION_PREFIX.'uid']."')";
		if ($condition != "") {
			$sql .= " and $condition ";
		}
		$sql .= " order by last_update DESC";
	}
} else if ($_GET['group_class'] == $_SESSION[SESSION_PREFIX.'gid']) {
	$sql = "select document_id,subject,created_by,last_update,filename from ".$GLOBALS['BR_document_table']."
		where group_class='".$_GET['group_class']."'";
	if ($condition != "") {
		$sql .= " and $condition ";
	}
	$sql .= " order by last_update DESC";
} else {
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		$sql = "select document_id,subject,created_by,last_update,filename from ".$GLOBALS['BR_document_table']."
			where group_class='".$_GET['group_class']."'";
		if ($condition != "") {
			$sql .= " and $condition ";
		}
		$sql .= " order by last_update DESC";
	} else {
		$sql = "select document_id,subject,created_by,last_update,filename from ".$GLOBALS['BR_document_table']."
			where (group_class='".$_GET['group_class']."' and 
			(allow_other_group='t' or created_by='".$_SESSION[SESSION_PREFIX.'uid']."'))";
		if ($condition != "") {
			$sql .= " and $condition";
		}
		$sql .= " order by last_update DESC";
	}
}

$sql_result = $GLOBALS['connection']->SelectLimit($sql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);

$user_array = GetAllUsers(1, 1);
// List all documents
$class_count = 0;
while ($row = $sql_result->FetchRow()) {
	$document_id = $row['document_id'];
	$subject = $row['subject'];
	$created_by = $row['created_by'];
	$last_update = $sql_result->UserTimeStamp($row['last_update'], GetDateTimeFormat());
	$filename = $row['filename'];
	$td_class = "line".($class_count%2);

	echo '
		<tr>
			<td class="'.$td_class.'">
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
			</td>
			<td class="'.$td_class.'">
				<a href="JavaScript:RedirectPage(\'document_show.php\', '.$document_id.');">'.$subject.'</a>
			</td>
			<td class="'.$td_class.'" align="center">
				'.UidToUsername($user_array, $created_by).'
			</td>
			<td class="'.$td_class.'" align="center">
				'.$last_update.'
			</td>
			<td class="'.$td_class.'" align="center">';
	if ($filename != "") {
		echo '
				<a href="document_download.php?document_id='.$document_id.'" target="_blank">';
		echo '
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/download.gif" width="16" height="16" title="'.$filename.'"></a>';
	} else {
		echo "&nbsp;";
	}
	echo '
			</td>
			<td class="'.$td_class.'" align="center">';

	if (($created_by == $_SESSION[SESSION_PREFIX.'uid']) ||
		($GLOBALS['Privilege'] & $GLOBALS['can_update_document']) ) {
		echo '
				<a href="JavaScript:RedirectPage(\'document_edit.php\', '.$document_id.');">
				'.$STRING['edit'].'</a>&nbsp;&nbsp;';
	}
	if (($created_by == $_SESSION[SESSION_PREFIX.'uid']) ||
		($GLOBALS['Privilege'] & $GLOBALS['can_delete_document']) ) {
		echo '
				<a href="JavaScript:ConfirmDelete('.$document_id.');">
				'.$STRING['delete'].'</a><br>';
	}

	echo '
			</td>
			<td class="'.$td_class.'">&nbsp;</td>
		</tr>';
	$class_count++;
}// end of while


?>
		<tr>
			<td width="100%" colspan="7" align="center">
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
include("../include/tail.php");
?>
