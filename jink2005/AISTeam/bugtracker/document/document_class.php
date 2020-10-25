<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: document_class.php,v 1.8 2008/11/30 03:46:28 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_manage_document_class'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}

?>
<script language="JavaScript" type="text/javascript">
<!--
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['document_class'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'document_class_delete.php?document_class_id='+id;
				}
				return;
			}
	});
	return;
}

function PopupInputNewClassCB(button)
{
	if (button == 'cancel') {
		return;
	}
	
	if (document.form_new.class_name.value.trim() == '') {
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_document_class'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['class_name'], $STRING['no_empty']));?>',
			buttons: ['ok'],
			width: 300,
			fn: function(button) {
				PopupInputNewClass();
			}
		});
		return;
	}
	ALEXWANG.Misc.DocumentMask();
	document.form_new.submit();
}

function PopupInputNewClass()
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['new_document_class'])?>',
			msg: '<form name="form_new" action="document_class_donew.php" method="POST">'+STRING['class_name']+STRING['colon']+
					'<br><input class="input-form-text-field" name="class_name" type="text" size="30" maxlength="50"><br>'+
					'<?php
						$hint = str_replace("@string@", $reserve_words, $STRING['reserve_hint']);
						$hint = str_replace("@key@", $STRING['class_name'], $hint);
						$hint = htmlspecialchars($hint);
						echo addslashes($hint);
					?>'+
				 '</form>',
			buttons: ['submit', 'cancel'],
			width: 300,
			fn: PopupInputNewClassCB
	});
	return;
}

function PopupEditClassCB(button)
{
	if (button == 'cancel') {
		return;
	}
	if (document.form_edit.class_name.value.trim() == '') {
		var id = document.form_edit.document_class_id.value;

		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_document_class'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['class_name'], $STRING['no_empty']));?>',
			buttons: ['ok'],
			width: 300,
			fn: function(button) {
				PopupEditClass(id, '');
			}

		});
		return;
	}
	ALEXWANG.Misc.DocumentMask();
	document.form_edit.submit();
}

function PopupEditClass(id, classname)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['edit_faq_class'])?>',
			msg: '<form name="form_edit" action="document_class_doedit.php" method="POST">'+STRING['class_name']+STRING['colon']+
					'<input name="document_class_id" type="hidden" value="'+id+'">'+
					'<br><input class="input-form-text-field" name="class_name" type="text" size="30" maxlength="50" value="'+classname+'"><br>'+
					'<?php
						$hint = str_replace("@string@", $reserve_words, $STRING['reserve_hint']);
						$hint = str_replace("@key@", $STRING['class_name'], $hint);
						$hint = htmlspecialchars($hint);
						echo addslashes($hint);
					?>'+
				 '</form>',
			buttons: ['submit', 'cancel'],
			width: 300,
			fn: PopupEditClassCB
	});
	return;
}
//-->
</script>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b>
	/ <a href="document.php"><?php echo $STRING['title_document']?></a> /
	<?php echo $STRING['document_class']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_document_class.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['document_class']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
			</td>
			<td nowrap valign="bottom">
				<a href="JavaScript:PopupInputNewClass();"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_document_class.png" border="0" align="middle"><?php echo $STRING['new_document_class']?></a>
				<a href="document.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">
				<tr>
					<td class="title" width="10">&nbsp;</td>
					<td align="center" width="350" class="title"><?php echo $STRING['class_name']?></td>
					<td align="center" width="100" class="title"><?php echo $STRING['function']?></td>
					<td class="title" width="10">&nbsp;</td>
				</tr>
<?php
$sql = "select * from ".$GLOBALS['BR_document_class_table']." order by class_name ASC";
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);;
$count=0;
while ($row = $result->FetchRow()) {
	$id = $row["document_class_id"];
	$class_name = $row["class_name"];
	$td_class="line".($count%2);

	echo '
				<tr>
					<td class="'.$td_class.'">
						<img border="0" src="'.$GLOBALS['SYS_URL_ROOT'].'/images/triangle_s.gif" width="8" height="9">
					</td>
					<td align="left" class="'.$td_class.'">'.$class_name.'</td>
					<td align="center" class="'.$td_class.'">
						<a href="JavaScript:PopupEditClass('.$id.', \''.addslashes($class_name).'\')">
						'.$STRING['edit'].'</a>&nbsp;&nbsp;
						<a href="JavaScript:ConfirmDelete('.$id.')">
						'.$STRING['delete'].'</a></font>
					</td>
					<td class="'.$td_class.'">&nbsp;</td>
				</tr>';
		$count++;
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
