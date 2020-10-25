<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_history_show.php,v 1.4 2013/06/29 11:33:40 alex Exp $
 *
 */
include("../include/header.php");
include("../include/user_function.php");
include("../include/datetime_function.php");

AuthCheckAndLogin();

class document_class {
	var $class_id;
	var $class_name;
}

if (!$_GET['document_history_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "id");
}

if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_document'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

$sql = "SELECT ".$GLOBALS['BR_document_history_table'].".*, ".$GLOBALS['BR_user_table'].".username
		FROM ".$GLOBALS['BR_document_history_table'].", ".$GLOBALS['BR_user_table']." WHERE
		".$GLOBALS['BR_document_history_table'].".created_by=".$GLOBALS['BR_user_table'].".user_id and
		document_history_id=".$GLOBALS['connection']->QMagic($_GET['document_history_id']);

$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();

if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document");
}
$document_id = $result->fields["document_id"];
$subject = $result->fields["subject"];
$description = $result->fields["description"];
$created_date = $result->UserTimeStamp($result->fields["created_date"], GetDateTimeFormat());
$filename = $result->fields["filename"];
$created_by = $result->fields["username"];

if ($_SESSION[SESSION_PREFIX.'uid'] != 0) {
	$sql = "select count(*) from ".$GLOBALS['BR_document_table']." 
			where document_id=".$document_id." and 
			(allow_other_group='t' or created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).")";
	
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	$line = $result->fields[0];
	if ($line != 1) {
		WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
		ErrorPrintOut("no_such_xxx", "document");
	}
}

?>
<script language="JavaScript" type="text/javascript">
<!--
function ConfirmRestore(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['document_restore'])?>',
			msg: '<?php echo addslashes($STRING['document_restore_confirm']);?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					document.form1.submit();
				}
				return;
			}
	});
	return;
}
//-->
</script>
<form method="GET" action="document_history_restore.php" name="form1">
	<input type="hidden" name="document_history_id" value="<?php echo $_GET['document_history_id']?>">
</form>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="document.php"><?php echo $STRING['title_document']?></a> /
	<a href="document_show.php?document_id=<?php echo $document_id?>"><?php echo $STRING['show_document']?></a> /
	<a href="document_history.php?document_id=<?php echo $document_id?>"><?php echo $STRING['history']?></a> /
	<?php echo $STRING['show_history_doc']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_document_history.png" width="48" height="48" align="middle" border="0">
				<a href="document_history.php?document_id=<?php echo $document_id?>">
					<tt class="outline"><?php echo $STRING['document_history']?></tt>
				</a>
			</td>

<?php
if ( ($GLOBALS['Privilege'] & $GLOBALS['can_update_document']) || 
	 ($created_by == $_SESSION[SESSION_PREFIX.'username']) ) {
	echo '
			<td nowrap valign="bottom">
				<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/document_restore.png" border="0" align="middle">
				<a href="JavaScript:ConfirmRestore('.$document_history_id.');">'.$STRING['document_restore'].'</a>
			</td>';
}
?>
			<td nowrap valign="bottom">
				<a href="document_history.php?document_id=<?php echo $document_id?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

		<div class="item_prompt_large"><?php echo $subject?></div>		
			<p>
<?php
echo '
				'.$STRING['created_by'].$STRING['colon'].$created_by.'<br>
				'.$STRING['created_date'].$STRING['colon'].$created_date.'<br><br>';

if ($filename) {
	echo '
				'.$STRING['file_upload'].$STRING['colon'];
	if ($GLOBALS['SYS_FILE_IN_DB'] == 1) {
		echo '
				<a href="document_download.php?document_history_id='.$_GET['document_history_id'].'" target="_blank">';
	} else {
		echo '
				<a href="documents/history/'.$filename.'" target="_blank">';
	}
	echo $filename.'</a>';
}
	
?>
			</p><hr size="1">
		<table width="95%">
			<tr><td><?php echo $description?></td></tr>
		</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php

include("../include/tail.php");
?>
