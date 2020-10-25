<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_edit.php,v 1.13 2013/07/07 21:28:42 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
if (!isset($_GET['customer_id'])) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "customer_id");
}
$sql = "select * from ".$GLOBALS['BR_customer_table']." where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id']);
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
if ($result->Recordcount() != 1) {
	WriteSyslog("error", "syslog_not_found", "customer", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "customer");
}
$customer_name = $result->fields["customer_name"];
$address = $result->fields["address"];
$tel = $result->fields["tel"];
$fax = $result->fields["fax"];
?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.form1;

	if(!f.customer_name.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['edit_customer'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['customer_name'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
		return false;
	}
	return OnSubmit(f);
}
function check_access_all()
{
	var checkall;
	if (document.form1.checkall.checked) {
		checkall = true;
	} else {
		checkall = false;
	}
<?php
	$project_array = GetAllProjects();
	for ($i=0; $i<sizeof($project_array); $i++) {
		echo "if (document.form1.project".$i.") { \n";
		echo "	document.form1.project".$i.".checked=checkall;";
		echo "} \n";
	}
?>
	return true;
}
-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b>/
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="customer_admin.php"><?php echo $STRING['customer_management']?></a> /
	<?php echo $STRING['edit_customer']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_customer.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['edit_customer']?></tt></td>
			<td nowrap width="100%" align="center" valign="bottom"></td>
			<td nowrap valign="bottom">
				<a href="customer_admin.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3><?php echo $STRING['edit_customer']?></h3>

			<form method="POST" action="customer_doedit.php" onsubmit="return check1();" name="form1">
				<input type="hidden" name="customer_id" value="<?php echo $_GET['customer_id']?>">

			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>
				<table class="table-input-form">
				<tr>
					<td width="33%" class="item_prompt_small"><?php echo $STRING['customer_name'].$STRING['colon']?></td>
					<td width="67%">
<?php
if ($_GET['customer_id'] == 0) {
	echo $customer_name;
	echo '<input type="hidden" name="customer_name" value="'.$customer_name.'">';
} else {
	echo '<input class="input-form-text-field" type="text" name="customer_name" size="50" value="'.$customer_name.'" maxlength="100">';
}		
?>
					</td>
				</tr>
				<tr>
					<td width="33%" class="item_prompt_small"><?php echo $STRING['address'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" type="text" name="address" size="50" value="<?php echo $address?>" maxlength="150"></td>
				</tr>
				<tr>
					<td width="33%" class="item_prompt_small"><?php echo $STRING['tel'].$STRING['colon']?></td>
					<td><input class="input-form-text-field"  type="text" name="tel" size="20" value="<?php echo $tel?>" maxlength="20"></td>
				</tr>
				<tr>
					<td width="33%" class="item_prompt_small"><?php echo $STRING['fax'].$STRING['colon']?></td>
					<td><input class="input-form-text-field" type="text" name="fax" size="20" value="<?php echo $fax?>" maxlength="20"></td>
				</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo $STRING['project_visible']?></legend>
				<table class="table-input-form">
				<tr>
					<td width="100%">
<?php
if (sizeof($project_array) > 0) {
	echo '<input type="checkbox" name="checkall" onclick="check_access_all();" class=checkbox>'.$STRING['select_all'];
} else {
	echo "&nbsp;";
}
      
?>
					</td>
				</tr>
<?php

// ��ܩҦ����Q�װϥH�ѿ��
for ($i=0; $i < sizeof($project_array); $i++) {
	$project_id = $project_array[$i]->getprojectid();
	$project_name = $project_array[$i]->getprojectname();

	$check_list = "select * from ".$GLOBALS['BR_proj_customer_access_table']." where customer_id=".$GLOBALS['connection']->QMagic($_GET['customer_id'])." and project_id='$project_id'";
	$check_result = $GLOBALS['connection']->Execute($check_list) or DBError(__FILE__.":".__LINE__);
	$check_line = $check_result->Recordcount();
	if ($check_line == 1) {
		$ischecked = "checked";
	}else{
		$ischecked = "";
	}
	echo '
				<tr><td>';
	echo '<input type="checkbox" name="project'.$i.'" value="'.$project_id.'" class="checkbox" '.$ischecked.'>';
	echo '&nbsp;'.htmlspecialchars($project_name).'</td>';
	echo "</tr>";
} // end of for
?>

			</table>
			</fieldset>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_submit']?>" name="B1" class="button"></p>
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div> 


<?php
include("../include/tail.php");
?>
