<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_history.php,v 1.5 2013/06/29 11:33:40 alex Exp $
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

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	$sql = "select count(*) from ".$GLOBALS['BR_document_table']."
			where document_id=".$_GET['document_id']." and 
			(allow_other_group='t' or created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).")";
	
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->fields[0];
	if ($line != 1) {
		WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
		ErrorPrintOut("no_such_xxx", "document");
	}
}

$sql = "SELECT count(*) FROM ".$GLOBALS['BR_document_history_table']." WHERE document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']);
$count_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$count = $count_result->fields[0];

?>
<script language="JavaScript" type="text/javascript">
<!--
function ChangeCondition() 
{
	form = document.form1;

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
	if (form.document_history_id.value == -1) {
		form.document_history_id.disabled = true;
	} else {
		form.document_history_id.disabled = false;
	}
	form.submit();
}

function ChangePage(page)
{
	document.form1.page.value = page;
	document.form1.document_id.value = <?php echo $_GET['document_id']?>;
	ChangeCondition();
}

function RedirectPage(action, document_history_id)
{
	document.form1.action = action;
	document.form1.document_history_id.value = document_history_id;
	ChangeCondition();
}

//-->
</script>
<form method="GET" action="<?php echo $_SERVER['PHP_SELF']?>" name="form1">
	<input type="hidden" name="page" value="-1">
	<input type="hidden" name="document_id" value="-1">
	<input type="hidden" name="document_history_id" value="-1">
</form>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="document.php"><?php echo $STRING['title_document']?></a> /
	<a href="document_show.php?document_id=<?php echo $_GET['document_id']?>"><?php echo $STRING['show_document']?></a> /
	<?php echo $STRING['history']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
	<tr>
		<td width="100%" align="left" nowrap>
			<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_document_history.png" width="48" height="48" align="middle" border="0">
			<a href="document_show.php?document_id=<?php echo $_GET['document_id']?>">
				<tt class="outline"><?php echo $STRING['document_history']?></tt>
			</a>
		</td>
		<td nowrap valign="bottom">
			<a href="document_show.php?document_id=<?php echo $_GET['document_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
		</td>
	</tr>
	</table>

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
			<td class="title" width="400" align="center"><?php echo $STRING['subject']?></td>
			<td class="title" width="100" align="center"><?php echo $STRING['created_by']?></td>
			<td class="title" width="100" align="center"><?php echo $STRING['created_date']?></td>
			<td class="title" align="center" width="80"><?php echo $STRING['file']?></td>
			<td class="title" width="10">&nbsp;</td>
		</tr>
	
<?php

$sql = "SELECT * FROM ".$GLOBALS['BR_document_history_table']." WHERE document_id='".$_GET['document_id']."' ORDER BY document_history_id DESC";
$sql_result = $GLOBALS['connection']->SelectLimit($sql, $perpage, $startat) or DBError(__FILE__.":".__LINE__);

$user_array = GetAllUsers(1, 1);
// List all documents
$class_count = 0;
while ($row = $sql_result->FetchRow()) {
	$document_history_id = $row['document_history_id'];
	$subject = $row['subject'];
	$created_by = $row['created_by'];
	$created_update = $sql_result->UserTimeStamp($row['created_date'], GetDateTimeFormat());
	$filename = $row['filename'];
	$td_class = "line".($class_count%2);

	echo '
		<tr>
			<td class="'.$td_class.'">
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/triangle_s.gif" width="8" height="9">
			</td>
			<td class="'.$td_class.'">
				<a href="JavaScript:RedirectPage(\'document_history_show.php\', '.$document_history_id.');">'.$subject.'</a>
			</td>
			<td class="'.$td_class.'" align="center">
				'.UidToUsername($user_array, $created_by).'
			</td>
			<td class="'.$td_class.'" align="center">
				'.$created_update.'
			</td>
			<td class="'.$td_class.'" align="center">';
	if ($filename != "") {
		echo '
				<a href="document_download.php?document_history_id='.$document_history_id.'" target="_blank">';
		echo '
				<img border="0" src="'.$GLOBALS["SYS_URL_ROOT"].'/images/download.gif" width="16" height="16" title="'.$filename.'"></a>';
	} else {
		echo "&nbsp;";
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
