<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: filter_new.php,v 1.18 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");
include("../include/status_function.php");
include("../include/user_function.php");
include("../include/customer_function.php");

AuthCheckAndLogin();

$status_array = GetStatusArray();
$userarray = GetAllUsers(1, 0);
$customer_array = GetAllCustomers();

// �ˬd�O�_�D�Ĥ@���s�쥻���A�p�G�D�Ĥ@���A�h�B�z�ϥΪ̩ҿ�J�� filter ���
if ((isset($_POST['next_op']) ) || isset($_POST['action'])) {
	// filter name �O�_���T
	if (trim($_POST['filter_name'] == "")) {
		ErrorPrintBackFormOut("GET", "filter_new.php", $_POST, 
							  "no_empty", "filter_name");
	}

	if (utf8_strlen($_POST['filter_name']) > 100) {
		ErrorPrintBackFormOut("GET", "filter_new.php", $_POST, 
							  "too_long", "filter_name", "100");
	}

	$this_filter = "";
	$this_text_filter = "";

	if ($_POST['type'] != 0) {
		$this_filter .= $_POST['merge_op']." type='".$_POST['type']."' ";
		$this_text_filter .= $_POST['merge_op']." type='".$STRING[$GLOBALS['type_array'][$_POST['type']]]."' ";
	}
	if ($_POST['created_date_op'] != "na") {
		if (checkdate($_POST['created_date_month'], $_POST['created_date_day'], $_POST['created_date_year']) == FALSE) {
			ErrorPrintBackFormOut("GET", "filter_new.php", $_POST, 
								  "wrong_format", "created_date");
		}
		$created_date = $_POST['created_date_year']."-".$_POST['created_date_month']."-".$_POST['created_date_day'];
		if ($_POST['created_date_op'] == "lt") {
			$op = "<";
		} elseif ($_POST['created_date_op'] == "gt") {
			$op = ">";
		} else {
			$op = "=";
		}
		$this_filter .= $_POST['merge_op']." created_date".$op."'".$created_date."' ";
		$this_text_filter .= $_POST['merge_op']." created_date".$op."'".$created_date."' ";
	}
	if ($_POST['last_update_op'] != "na") {
		if (checkdate($_POST['last_update_month'], $_POST['last_update_day'], $_POST['last_update_year']) == FALSE) {
			ErrorPrintBackFormOut("GET", "filter_new.php", $_POST, 
								  "wrong_format", "last_update");
		}
		$last_update = $_POST['last_update_year']."-".$_POST['last_update_month']."-".$_POST['last_update_day'];
		if ($_POST['last_update_op'] == "lt") {
			$op = "<";
		} elseif ($_POST['last_update_op'] == "gt") {
			$op = ">";
		} else {
			$op = "=";
		}
		$this_filter .= $_POST['merge_op']." last_update".$op."'".$last_update."' ";
		$this_text_filter .= $_POST['merge_op']." last_update".$op."'".$last_update."' ";
	} // end of last update
	if (($_POST['priority_op'] != "na") && ($_POST['priority'] != 0)){
		if ($_POST['priority_op'] == "gt") {
			$_POST['priority_op'] = ">";
			$op = ">";
		} elseif($_POST['priority_op'] == "lt") {
			$_POST['priority_op'] = "<";
			$op = "<";
		} elseif($_POST['priority_op'] == "ne") {
			$_POST['priority_op'] = "!=";
			$op = "!=";
		} else {
			$_POST['priority_op'] = "=";
			$op = "=";
		}
		$this_filter .= $_POST['merge_op']." priority".$op."'".$_POST['priority']."' ";
		$this_text_filter .= $_POST['merge_op']." priority".$_POST['priority_op']."'".$STRING[$GLOBALS['priority_array'][$_POST['priority']]]."' ";
	}
	if ($_POST['status'] != 0) {
		if ($_POST['status'] == "-1") {
			$this_filter .= $_POST['merge_op']." status in (select status_id from ".$GLOBALS['BR_status_table']." where status_type='active') ";
			$this_text_filter .= $_POST['merge_op']." status='".$STRING['all_active_bugs']."' ";
		} else if ($_POST['status'] == "-2") {
			$this_filter .= $_POST['merge_op']." status in (select status_id from ".$GLOBALS['BR_status_table']." where status_type='closed') ";
			$this_text_filter .= $_POST['merge_op']." status='".$STRING['all_closed_bugs']."' ";
		} else {
			$this_filter .= $_POST['merge_op']." status=".$_POST['status']." ";
			$this_text_filter .= $_POST['merge_op']." status='".GetStatusNameByID($status_array, $_POST['status'])."' ";
		}
		
	}
	if ($_POST['reported_by'] != -1) {
		$this_filter .= $_POST['merge_op']." reported_by=".$_POST['reported_by']." ";
		$this_text_filter .= $_POST['merge_op']." reported_by='".UidToUsername($userarray, $_POST['reported_by'])."' ";
	}
	if ($_POST['assign_to'] != -1) {
		$this_filter .= $_POST['merge_op']." assign_to=".$_POST['assign_to']." ";
		$this_text_filter .= $_POST['merge_op']." assign_to='".UidToUsername($userarray, $_POST['assign_to'])."' ";
	}
	if ($_POST['fixed_by'] != -1) {
		$this_filter .= $_POST['merge_op']." fixed_by=".$_POST['fixed_by']." ";
		$this_text_filter .= $_POST['merge_op']." fixed_by='".UidToUsername($userarray, $_POST['fixed_by'])."' ";
	}
	if ($_POST['fixed_date_op'] != "na") {
		if (checkdate($_POST['fixed_date_month'], $_POST['fixed_date_day'], $_POST['fixed_date_year']) == FALSE) {
			ErrorPrintBackFormOut("GET", "filter_new.php", $_POST, 
								  "wrong_format", "fixed_date");
		}
		$fixed_date = $_POST['fixed_date_year']."-".$_POST['fixed_date_month']."-".$_POST['fixed_date_day'];

		if ($_POST['fixed_date_op'] == "gt") {
			$op = ">";
		} elseif($_POST['fixed_date_op'] == "lt") {
			$op = "<";
		} else {
			$op = "=";
		}
		$this_filter .= $_POST['merge_op']." fixed_date".$op."'".$fixed_date."' ";
		$this_text_filter .= $_POST['merge_op']." fixed_date".$op."'".$fixed_date."' ";
	}
	 
	if ( (trim($_POST['version']) != "") && ($_POST['version_op'] != "na")) {
		if ($_POST['version_op'] == "gt") {
			$op = ">";
		} elseif($_POST['version_op'] == "lt") {
			$op = "<";
		} else {
			$op = "=";
		}
		$this_filter .= $_POST['merge_op']." version".$op."'".$_POST['version']."' ";
		$this_text_filter .= $_POST['merge_op']." version".$op."'".$_POST['version']."' ";
	}
	if ( (trim($_POST['fixed_in_version']) != "") && ($_POST['fixed_in_version_op'] != "na")) {
		if ($_POST['fixed_in_version_op'] == "gt") {
			$op = ">";
		} elseif($_POST['fixed_in_version_op'] == "lt") {
			$op = "<";
		} else {
			$op = "=";
		}
		$this_filter .= $_POST['merge_op']." fixed_in_version".$op."'".$_POST['fixed_in_version']."' ";
		$this_text_filter .= $_POST['merge_op']." fixed_in_version".$op."'".$_POST['fixed_in_version']."' ";
	}
	if ($_POST['reported_by_customer'] != -1) {
		$this_filter .= $_POST['merge_op']." reported_by_customer='".$_POST['reported_by_customer']."' ";
		$this_text_filter .= $_POST['merge_op']." reported_by_customer='".GetCustomerNameFromID($customer_array, $_POST['reported_by_customer'])."' ";
	}

	// �?��J����,��s filter ����ơA�çP�_�O�_���Ĥ@�� filter list
	// �ӨM�w�O�_�n�H operator �s��
	if ($this_filter != "") {
		$this_filter = strstr($this_filter, " ");
		$this_text_filter = strstr($this_text_filter, " ");

		$this_filter = "(".$this_filter.")";
		$this_text_filter = "(".$this_text_filter.")";
		if ($_SESSION[SESSION_PREFIX.'new_filter']['real_condition'] != "") {
		   $_SESSION[SESSION_PREFIX.'new_filter']['real_condition'] = $_SESSION[SESSION_PREFIX.'new_filter']['real_condition']." ".$this_filter." ".$_POST['next_op'];
		   $_SESSION[SESSION_PREFIX.'new_filter']['text_condition'] = $_SESSION[SESSION_PREFIX.'new_filter']['text_condition']." ".$this_text_filter." ".$_POST['next_op'];
		}else{
		   $_SESSION[SESSION_PREFIX.'new_filter']['real_condition'] = $this_filter." ".$_POST['next_op'];
		   $_SESSION[SESSION_PREFIX.'new_filter']['text_condition'] = $this_text_filter." ".$_POST['next_op'];
		}
	}
	// �ѩ� PHP �|�۰��� ' �[�W \ �� \'
	// �ҥH�ڭ̥H stripslashes()�ӥh�� filter_name �r�ꤤ���Ҧ��׽u
	if (get_magic_quotes_gpc()) {
		$_POST['filter_name'] = stripslashes($_POST['filter_name']);
	}
	
	$_POST['filter_name'] = str_replace('"', "&quot;", $_POST['filter_name']);
} else {// end of is empty? $merge_by
	unset($_SESSION[SESSION_PREFIX.'new_filter']);
}


// �p�G�|���� Done�A�h�~����ܿ�J���
if (!$_POST['action']) {
	if (isset($_GET['real_condition'])) {
		$filter_name = $_GET['filter_name'];
		if (get_magic_quotes_gpc()) {
			$filter_name = stripslashes($filter_name);
		}
	} else {
		$filter_name = $_POST['filter_name'];
	}
	$count_sql = "select * from ".$GLOBALS['BR_filter_table']." 
		where user_id='".$_SESSION[SESSION_PREFIX.'uid']."' and share_filter='t'";
	$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
	$count_share_filter = $count_result->Recordcount();

?>
<script language="JavaScript" type="text/javascript">
<!--
function check1()
{
    var f=document.form1;
    if(f.filter_name.value) {
        return OnSubmit(f);
    } else {
        ALEXWANG.Dialog.Show({
			title: '<?php echo addslashes($STRING['new_filter'])?>',
			msg: '<?php echo addslashes(str_replace("@key@", $STRING['filter_name'], $STRING['no_empty']));?>',
			width: 300,
			buttons: ['ok']
		});
        return false;
    }
}

function onCancel()
{
	parent.location='filter_setting.php';
}
//-->
</script>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="filter_setting.php"><?php echo $STRING['set_filter']?></a> /
	<?php echo $STRING['new_filter']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_filter.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['set_filter']?></tt>
			</td>
			<td nowrap valign="bottom"  align="right" width="100%">
<?php
if ($_REQUEST['project_id'] != "") {
	echo '
				<a href="filter_setting.php?project_id='.$_REQUEST['project_id'].'">';
} else {
	echo '
				<a href="filter_setting.php">';
}
?>
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

<?php
if ($_SESSION[SESSION_PREFIX.'new_filter']['text_condition'] != "") {
	echo '

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>'.$STRING['current_filter'].'</h3>
			<table class="table-input-form">
			<tr>
				<td width="100%">'.$_SESSION[SESSION_PREFIX.'new_filter']['text_condition'].'</td>
			</tr>
			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>';
}
?>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

			<h3>&nbsp;</h3>
			<form method="POST" action="filter_new.php" onsubmit="return check1();" name="form1">
			<input type="hidden" name="project_id" value="<?php echo $_REQUEST['project_id']?>">

				<table class="table-input-form">

<?php
if ($count_share_filter < $SYSTEM['max_shared_filter']) {
?>
		
				<tr>
					<td width="210" class="item_prompt_small">
						<?php echo $STRING['share_filter'].$STRING['colon']?>
					</td>
					<td width="290">
<?php
	if ($_POST['share_filter'] == 't') {
		echo '
						<input type="radio" value="t" checked name="share_filter" class="checkbox">'.$STRING['yes'].' &nbsp;&nbsp;&nbsp
						<input type="radio" name="share_filter" value="f" class=checkbox>'.$STRING['no'];
	} else {
		echo '
						<input type="radio" value="t"  name="share_filter" class="checkbox">'.$STRING['yes'].' &nbsp;&nbsp;&nbsp
						<input type="radio" name="share_filter" checked value="f" class=checkbox>'.$STRING['no'];
	}
?>
			
					</td>
				</tr>
<?php
}
?>

				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['filter_name'].$STRING['colon']?>
					</td>
					<td>
						<input class="input-form-text-field" type="text" name="filter_name" size="20" maxlength="100" value="<?php echo $filter_name?>">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['type'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="type">
<?php
	for ($i=0; $i<sizeof($GLOBALS['type_array']); $i++) {
		echo '
							<option value='.$i.'>'.$STRING[$GLOBALS['type_array'][$i]].'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['created_date'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="created_date_op">
							<option value="na"></option>
							<option value="lt"><?php echo $STRING['date_before']?></option>
							<option value="gt"><?php echo $STRING['date_after']?></option>
							<option value="eq"><?php echo $STRING['equals_to']?></option>
						</select>
						<select size="1" name="created_date_year">
<?php
	for ($i=2002;$i<=date("Y");$i++) {
		echo '
							<option>'.$i.'</option>';
	}
	echo '
						</select> /
						<select size="1" name="created_date_month">';
	for ($i=1;$i<=12;$i++) {
		echo '
							<option>'.$i.'</option>';
	}
	echo '
						</select> /
						<select size="1" name="created_date_day">';
	for ($i=1;$i<=31;$i++) {
		echo '
							<option>'.$i.'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['last_update'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="last_update_op">
							<option value="na"></option>
							<option value="lt"><?php echo $STRING['date_before']?></option>
							<option value="gt"><?php echo $STRING['date_after']?></option>
							<option value="eq"><?php echo $STRING['equals_to']?></option>
						</select>
						<select size="1" name="last_update_year">
<?php
	for ($i=2002;$i<=date("Y");$i++) {
		echo '
							<option>'.$i.'</option>';
	}
	echo '
						</select> /
						<select size="1" name="last_update_month">';
	for ($i=1;$i<=12;$i++) {
		echo '
							<option>'.$i.'</option>';
	}
	echo '
						</select> /
						<select size="1" name="last_update_day">';
	for ($i=1;$i<=31;$i++) {
		echo '
							<option>'.$i.'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['priority'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="priority_op">
							<option value="na"></option>
							<option value="gt"><?php echo $STRING['greater_than']?></option>
							<option value="lt"><?php echo $STRING['less_than']?></option>
							<option value="eq"><?php echo $STRING['equals_to']?></option>
							<option value="ne"><?php echo $STRING['not_equals_to']?></option>
						</select>
						<select size="1" name="priority">
							<option value="0"></option>
<?php
	for($i = (sizeof($GLOBALS['priority_array']) - 1); $i > 0; $i--){
		echo '
							<option value="'.$i.'">'.$STRING[$GLOBALS['priority_array'][$i]].'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['status'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="status">
							<option value="0"></option>
							<option value="-1">-<?php echo $STRING['all_active_bugs']?></option>
							<option value="-2">-<?php echo $STRING['all_closed_bugs']?></option>
<?php
	for ($i=0; $i<sizeof($status_array); $i++) {
		$status_id= $status_array[$i]->getstatusid();
		$status_name= htmlspecialchars($status_array[$i]->getstatusname());
		echo '
							<option value="'.$status_id.'">'.$status_name.'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['reported_by'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="reported_by">
							<option value="-1"></option>
<?php
	for ($i=0;$i<sizeof($userarray);$i++) {
		echo '
							<option value="'.$userarray[$i]->getuserid().'">'.$userarray[$i]->getusername().'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['assign_to'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="assign_to">
							<option value="-1"></option>
<?php
	for ($i=0;$i<sizeof($userarray);$i++) {
		echo '
							<option value="'.$userarray[$i]->getuserid().'">'.$userarray[$i]->getusername().'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['fixed_by'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="fixed_by">
							<option value="-1"></option>
<?php
	for ($i=0;$i<sizeof($userarray);$i++) {
		echo '
							<option value="'.$userarray[$i]->getuserid().'">'.$userarray[$i]->getusername().'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['fixed_date'].$STRING['colon']?>
					</td>
					<td nowrap>
						<select size="1" name="fixed_date_op">
							<option value="na"></option>
							<option value="lt"><?php echo $STRING['date_before']?></option>
							<option value="gt"><?php echo $STRING['date_after']?></option>
							<option value="eq"><?php echo $STRING['equals_to']?></option>
						</select>
						<select size="1" name="fixed_date_year">
<?php
	for ($i=2002;$i<=date("Y");$i++) {
		echo '
							<option>'.$i.'</option>';
	}
	echo '
						</select> /
						<select size="1" name="fixed_date_month">';
	for ($i=1;$i<=12;$i++) {
		echo '
							<option>'.$i.'</option>';
	}
	echo '
						</select> /
						<select size="1" name="fixed_date_day">';
	for ($i=1;$i<=31;$i++) {
		echo '
							<option>'.$i.'</option>';
	}
?>

						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['version'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="version_op">
							<option value="na"></option>
							<option value="lt"><?php echo $STRING['date_before']?></option>
							<option value="gt"><?php echo $STRING['date_after']?></option>
							<option value="eq"><?php echo $STRING['equals_to']?></option>
						</select>
						<input class="input-form-text-field" type="text" name="version" size="20" maxlength="40">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['fixed_in_version'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="fixed_in_version_op">
							<option value="na"></option>
							<option value="lt"><?php echo $STRING['date_before']?></option>
							<option value="gt"><?php echo $STRING['date_after']?></option>
							<option value="eq"><?php echo $STRING['equals_to']?></option>
						</select>
						<input class="input-form-text-field" type="text" name="fixed_in_version" size="20" maxlength="40">
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['reported_by_customer'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="reported_by_customer">
							<option value="-1"></option>
<?php
	for ($i=0; $i<sizeof($customer_array); $i++) {
		echo '
							<option value="'.$customer_array[$i]->getcustomerid().'">
							'.$customer_array[$i]->getcustomername().'</option>';
	}
?>
                    
						</select>
					</td>
				</tr>
				<tr>
					<td class="item_prompt_small">
						<?php echo $STRING['merge_above_op'].$STRING['colon']?>
					</td>
					<td>
						<select size="1" name="merge_op">
							<option>AND</option>
							<option>OR</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p align="center">
							<?php echo $STRING['next_filter_hint'].$STRING['colon']?><br>
							<input type="submit" value="AND" name="next_op" class="button">
							<input type="submit" value="OR" name="next_op" class="button">
						</p>
					</td>
				</tr>
				</table>
				<p align="center">
					<input type="submit" value="<?php echo $STRING['button_done']?>" name="action" class="button">
				</p>
			</form>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
} // end of no action

// ���U donew �F,�ҥH����s�W���ʧ@
if ($_POST['action']) {
	// ��p��ϥΪ��� filter �O�_�b $MAX_FILTER_NUM �ӥH���A�̦h�u�ह�\ $MAX_FILTER_NUM ��
	$count_sql = "select count(*) from ".$GLOBALS['BR_filter_table']." where user_id='".$_SESSION[SESSION_PREFIX.'uid']."'";
	$count_result = $GLOBALS['connection']->Execute($count_sql) or DBError(__FILE__.":".__LINE__);
	$count = $count_result->fields[0];
	if ($count > $SYSTEM['max_filter_per_user']) {
		ErrorPrintOut("no_such_xxx", "filter");
	}
	$real_condition = $_SESSION[SESSION_PREFIX.'new_filter']['real_condition'];
	$text_condition = $_SESSION[SESSION_PREFIX.'new_filter']['text_condition'];
	if (!$_SESSION[SESSION_PREFIX.'new_filter']['real_condition']) {
		ErrorPrintOut("no_empty", "condition");
	}
	
	if (substr($real_condition, -2, 2) == "OR") {
		$real_condition = substr($real_condition, 0, strlen($real_condition)-3);
		$text_condition = substr($text_condition, 0, strlen($text_condition)-3);
	} elseif(substr($real_condition, -3, 3) == "AND") {
		$real_condition = substr($real_condition, 0, strlen($real_condition)-4);
		$text_condition = substr($text_condition, 0, strlen($text_condition)-4);
	}

	if (!isset($_POST['share_filter'])) {
		$_POST['share_filter'] = "f";
	}

	if (get_magic_quotes_gpc()) {
		$_POST['filter_name'] = addslashes($_POST['filter_name']);
	}

	$filter_sql = "insert into ".$GLOBALS['BR_filter_table']."(user_id, filter_name, real_condition, text_condition, share_filter) 
			values(".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']).", 
			".$GLOBALS['connection']->QMagic(htmlspecialchars($_POST['filter_name'])).",
			".$GLOBALS['connection']->QMagic($real_condition).", 
			".$GLOBALS['connection']->QMagic($text_condition).", 
			".$GLOBALS['connection']->QMagic($_POST['share_filter']).")";
	$filter_result = $GLOBALS['connection']->Execute($filter_sql) or DBError(__FILE__.":".__LINE__);

	if ($_REQUEST['project_id'] != "") {
		FinishPrintOut("filter_setting.php?project_id=".$_REQUEST['project_id'], "finish_new", "filter");
	} else {
		FinishPrintOut("filter_setting.php", "finish_new", "filter");
	}

}// end of donew

include("../include/tail.php");
?>
