<?php 
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_class.php,v 1.13 2013/06/29 20:18:15 alex Exp $
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
?>
<script language="JavaScript" type="text/javascript">
<!--
function ConfirmDelete(id)
{
	ALEXWANG.Dialog.Show({
		title: '<?php echo addslashes($STRING['delete'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['faq_class'], $STRING['delete_note']));?>',
			buttons: ['yes', 'no'],
			width: 300,
			fn: function(button) {
				if (button == 'yes') {
					location.href= 'faq_class_delete.php?faq_class_id='+id;
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
			title: '<?php echo addslashes($STRING['new_faq_class'])?>',
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
		title: '<?php echo addslashes($STRING['new_faq_class'])?>',
		msg: '<form name="form_new" action="faq_class_donew.php" method="POST">'+STRING['class_name']+STRING['colon']+
					'<input name="project_id" type="hidden" value="<?php echo $_GET['project_id']?>">'+
					'<br><input class="input-form-text-field" name="class_name" type="text" size="40" maxlength="50"><br>'+
					'<?php 
						$hint = str_replace("@string@", $reserve_words, $STRING['reserve_hint']);
						$hint = str_replace("@key@", $STRING['class_name'], $hint);
						$hint = htmlspecialchars($hint);
						echo addslashes($hint);
					?>'+
				 '</form>',
		buttons: ['create', 'cancel'],
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
		var id = document.form_edit.faq_class_id.value;

		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_faq_class'])?>',
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
		msg: '<form name="form_edit" action="faq_class_doedit.php" method="POST">'+STRING['class_name']+STRING['colon']+
					'<input name="project_id" type="hidden" value="<?php echo $_GET['project_id']?>">'+
					'<input name="faq_class_id" type="hidden" value="'+id+'">'+
					'<br><input class="input-form-text-field" name="class_name" type="text" size="40" maxlength="50" value="'+classname+'"><br>'+
					'<?php 
						$hint = str_replace("@string@", $reserve_words, $STRING['reserve_hint']);
						$hint = str_replace("@key@", $STRING['class_name'], $hint);
						$hint = htmlspecialchars($hint);
						echo addslashes($hint);
					?>'+
				 '</form>',
		buttons: ['create', 'cancel'],
		width: 300,
		fn: PopupEditClassCB
	});
	return;
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b>
	/ <a href="../index.php"><?php echo $STRING['title_project_list']?></a> /
	<a href="faq_admin.php?project_id=<?php echo $_GET['project_id']?>"><?php echo $project_name?>
	<?php echo $STRING['faq']?></a> / <?php echo $STRING['faq_class']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_faq_class.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['faq_class']?></tt>
			</td>
			<td nowrap width="100%" align="center" valign="bottom">
			</td>
			<td nowrap valign="bottom">
				<a href="JavaScript:PopupInputNewClass();">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/new_faq_class.png" border="0" align="middle"><?php echo $STRING['new_faq_class']?></a>
				<a href="faq_admin.php?project_id=<?php echo $_GET['project_id']?>"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
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
	$sql = "select * from ".$GLOBALS['BR_faq_class_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." order by class_name ASC";
	$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);;
	$count=0;
	while ($row = $result->FetchRow()) {
		$id = $row["faq_class_id"];
		$class_name = $row["class_name"];
		$td_class="line".($count%2);
      
		echo '
			<tr>
				<td class="'.$td_class.'">
					<img border="0" src="'.$GLOBALS['SYS_URL_ROOT'].'/images/triangle_s.gif" width="8" height="9">
				</td>
				<td align="left" class="'.$td_class.'">'.$class_name.'</td>
				<td align="center" class="'.$td_class.'">
					<a href="JavaScript:PopupEditClass('.$id.', \''.addslashes($class_name).'\')">'.$STRING['edit'].'</a>&nbsp;&nbsp;
					<a href="JavaScript:ConfirmDelete('.$id.');">'.$STRING['delete'].'</a>
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
