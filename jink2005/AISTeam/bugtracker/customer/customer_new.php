<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: customer_new.php,v 1.11 2013/07/07 21:28:42 alex Exp $
 *
 */
include("../include/header.php");
include("../include/project_function.php");

AuthCheckAndLogin();

if (!($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer'])) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_privilege");
}
	
?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
	var f=document.form1;

	if(!f.customer_name.value){
		ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_customer'])?>',
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
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="customer_admin.php"><?php echo $STRING['customer_management']?></a> / <?php echo $STRING['new_customer']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
			<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_customer.png" width="48" height="48" align="middle" border="0">
			<tt class="outline"><?php echo $STRING['new_customer']?></tt></td>
			<td nowrap width="100%" align="center" valign="bottom"></td>
			<td nowrap valign="bottom">
			<a href="customer_admin.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>
	
	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3><?php echo $STRING['new_customer']?></h3>
			<form method="POST" action="customer_donew.php" onsubmit="return check1();" name="form1">

			<fieldset>
				<legend><?php echo $STRING['basic_information']?></legend>
				<table class="table-input-form">
				<tr>
					<td width="33%" class="item_prompt_small">
						<?php echo $STRING['customer_name'].$STRING['colon']?>
					</td>
					<td width="67%">
						<input class="input-form-text-field" type="text" name="customer_name" size="50" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['customer_name']?>" maxlength="100">
					</td>
				</tr>
				<tr>
					<td width="33%" class="item_prompt_small">
						<?php echo $STRING['address'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" name="address" size="50" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['address']?>" maxlength="150">
					</td>
				</tr>
				<tr>
					<td width="33%" class="item_prompt_small">
						<?php echo $STRING['tel'].$STRING['colon']?>
					</td>
					<td>
						<input name="tel" class="input-form-text-field" size="20" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['tel']?>" maxlength="20">
					</td>
				</tr>
				<tr>
					<td width="33%" class="item_prompt_small">
						<?php echo $STRING['fax'].$STRING['colon']?>
					</td>
					<td>
						<input name="fax" class="input-form-text-field" size="20" value="<?php echo $_SESSION[SESSION_PREFIX.'back_array']['fax']?>" maxlength="20">
					</td>
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
	echo '<input type="checkbox" name="checkall" onclick="check_access_all();" class=checkbox>'.$STRING['select_all'].'<br>';
} else {
	echo "&nbsp;";
}
echo '
					</td>';
// ��ܩҦ����Q�װϥH�ѿ��
for ($i=0; $i<sizeof($project_array); $i++) {
	echo "<tr>";
	echo '<td>';
	$project_id = $project_array[$i]->getprojectid();
	$project_name = $project_array[$i]->getprojectname();
	if (isset($_SESSION[SESSION_PREFIX.'back_array']['project'.$i]) && ($_SESSION[SESSION_PREFIX.'back_array']['project'.$i] == $project_id)) {
		echo '<input type="checkbox" name="project'.$i.'" value="'.$project_id.'" class="checkbox" checked>';
	} else {
		echo '<input type="checkbox" name="project'.$i.'" value="'.$project_id.'" class="checkbox">';
	}
	
	//echo "&nbsp;".$project_name.'<br>';
	echo htmlspecialchars($project_name).'</td>';
		echo "</tr>\n";
}
?>
				</table>
			</fieldset>
			<p align="center"><input type="submit" value="<?php echo $STRING['button_create']?>" name="B1" class="button"></p>
		</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>      

<?php
include("../include/tail.php");
?>
