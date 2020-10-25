<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_show.php,v 1.18 2013/06/29 11:33:40 alex Exp $
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

if (!$_GET['document_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "document_id");
}
if (!($GLOBALS['Privilege'] & $GLOBALS['can_see_document'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
	$sql = "select ".$GLOBALS['BR_document_table'].".subject,".
			$GLOBALS['BR_document_table'].".description,".
			$GLOBALS['BR_document_table'].".last_update, ".
			$GLOBALS['BR_document_table'].".filename, ".
			$GLOBALS['BR_user_table'].".username
			from ".$GLOBALS['BR_document_table'].", ".$GLOBALS['BR_user_table']." 
			where document_id='".$_GET['document_id']."' and 
			".$GLOBALS['BR_document_table'].".created_by=".$GLOBALS['BR_user_table'].".user_id";
} else {
	$sql = "select ".$GLOBALS['BR_document_table'].".subject,".
			$GLOBALS['BR_document_table'].".description,".
			$GLOBALS['BR_document_table'].".last_update, ".
			$GLOBALS['BR_document_table'].".filename, ".
			$GLOBALS['BR_user_table'].".username
			from ".$GLOBALS['BR_document_table'].", ".$GLOBALS['BR_user_table']." 
			where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id'])." and 
			".$GLOBALS['BR_document_table'].".created_by=".$GLOBALS['BR_user_table'].".user_id and
			(allow_other_group='t' or created_by=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).")";
}

$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	WriteSyslog("error", "syslog_not_found", "document", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "document");
}
$subject = $result->fields["subject"];
$description = $result->fields["description"];

$last_update = $result->UserTimeStamp($result->fields["last_update"], GetDateTimeFormat());
$filename = $result->fields["filename"];
$created_by = $result->fields["username"];

$old_class_sql = "select * from ".$GLOBALS['BR_document_class_table']." where document_class_id in
			(select document_class_id from ".$GLOBALS['BR_document_map_table']." where document_id=".$GLOBALS['connection']->QMagic($_GET['document_id']).")
			order by class_name";
$old_class_result = $GLOBALS['connection']->Execute($old_class_sql) or DBError(__FILE__.":".__LINE__);
$class_array = array();
while ($row = $old_class_result->FetchRow()) {
	$class = new document_class;
	$class->class_id = $row["document_class_id"];
	$class->class_name = $row["class_name"];
	array_push($class_array, $class);
}

$extra_params = GetExtraParams($_GET, "search_key,group_class,page");

?>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="document.php"><?php echo $STRING['title_document']?></a> /
	<?php echo $STRING['show_document']?>
</div>
<div id="main_container">
		<table width="100%" border="0">
			<tr>
				<td width="100%" align="left" nowrap>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_document.png" width="48" height="48" align="middle" border="0">
				<a href="document.php<?php if ($extra_params != "") {echo "?".substr($extra_params, 1);}?>">
					<tt class="outline"><?php echo $STRING['title_document']?></tt>
				</a></td>

<?php
if ( ($GLOBALS['Privilege'] & $GLOBALS['can_update_document']) || 
	 ($created_by == $_SESSION[SESSION_PREFIX.'username']) ) {
	echo '<td nowrap valign="bottom">';
	echo "<img src=\"".$GLOBALS["SYS_URL_ROOT"]."/images/update.png\" border=\"0\" align=\"middle\">";
	echo "<a href=\"document_edit.php?document_id=".$_GET['document_id'].$extra_params."&from_show=y\">".$STRING['edit_document']."</a>";
	echo '</td>';
}
?>
				<td nowrap valign="bottom">
				<a href="document_history.php?document_id=<?php echo $_GET['document_id'].$extra_params?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/history.png" border="0" align="middle"><?php echo $STRING['history']?></a>
				</td>
				<td nowrap valign="bottom">
				<a href="document.php<?php if ($extra_params != "") {echo "?".substr($extra_params, 1);}?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
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
				'.$STRING['last_update'].$STRING['colon'].$last_update.'<br>
				'.$STRING['document_class'].$STRING['colon'];
foreach ($class_array as $class) {
	echo '
				<a href="document.php?document_class='.$class->class_id.'">'.$class->class_name.'</a>, ';
}
echo '<br>';
				

if ($filename) {
	echo '
				'.$STRING['file_upload'].$STRING['colon'];
	if ($GLOBALS['SYS_FILE_IN_DB'] == 1) {
		echo '
				<a href="document_download.php?document_id='.$_GET['document_id'].'" target="_blank">';
	} else {
		echo '
				<a href="documents/'.$filename.'" target="_blank">';
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
