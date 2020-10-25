<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: system.php,v 1.9 2008/11/28 10:36:31 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if ($_GET['page'] == "information") {
	if ($GLOBALS['Privilege'] & $GLOBALS['can_see_sysinfo']) {
		$show_function['system_info'] = "system/system_info.php";
		$show_function['syslog'] = "system/syslog.php";
		$show_function['feedback_syslog'] = "system/syslog.php?feedback=1";
	}
	if ($GLOBALS['Privilege'] & $GLOBALS['can_see_statistic']) {
		$show_function['login_statistic'] = "system/login_statistic.php";
		$show_function['statistic_new_report'] = "system/statistic_new_report.php";
		$show_function['statistic_status'] = "system/statistic_status.php";
		$show_function['statistic_feedback_report'] = "system/statistic_feedback_report.php";
	}
	$show_function['system_about'] = "system/system_about.php";
	
	if ($GLOBALS['Privilege'] & $GLOBALS['can_see_sysinfo']) {
		$function_pic['system_info'] = "system_info.png";
		$function_pic['syslog'] = "system_syslog.png";
		$function_pic['feedback_syslog'] = "system_feedback_syslog.png";
	}
	if ($GLOBALS['Privilege'] & $GLOBALS['can_see_statistic']) {
		$function_pic['login_statistic'] = "statistic_login.png";
		$function_pic['statistic_new_report'] = "statistic_new_report.png";
		$function_pic['statistic_status'] = "statistic_status.png";
		$function_pic['statistic_feedback_report'] = "statistic_feedback.png";
	}
	$function_pic['system_about'] = "system_about.png";

} else {
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		$show_function['system_config'] = "system/system_config.php";
	}
	if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_user']) {
		$show_function['group_management'] = "user/group_admin.php";
		$show_function['user_management'] = "user/user_admin.php";
	} elseif ($GLOBALS['Privilege'] & $GLOBALS['can_edit_selfdata']) {
		$show_function['my_account'] = "user/user_edit.php";
	}
	if ($_SESSION[SESSION_PREFIX.'uid'] == 0) {
		$show_function['feedback_system'] = "system/feedback_config.php";
	}
	if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_customer']) {
		$show_function['customer_management'] = "customer/customer_admin.php";
	}
	if ($GLOBALS['Privilege'] & $GLOBALS['can_admin_status']) {
		$show_function['status_management'] = "system/status_admin.php";
	}
	$show_function['preference'] = "user/user_setting.php";
	$show_function['set_filter'] = "user/filter_setting.php";


	$function_pic['system_config'] = "system_config.png";
	$function_pic['group_management'] = "system_group.png";
	$function_pic['user_management'] = "system_user.png";
	$function_pic['my_account'] = "system_user.png";
	$function_pic['feedback_system'] = "system_feedback.png";
	$function_pic['customer_management'] = "system_customer.png";
	$function_pic['status_management'] = "system_status.png";
	$function_pic['preference'] = "system_preference.png";
	$function_pic['set_filter'] = "system_filter.png";
	
}

?>

<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_system.png" width="48" height="48" align="middle" border="0">
				<tt class="outline">
<?php
if ($_GET['page'] == "information") {
	echo $STRING['title_information'];
} else {
	echo $STRING['title_system'];
}
?>

				</tt>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">

<?php
$count = 0;
$function_key = array_keys($show_function);
for ($i=0; $i<sizeof($function_key); $i++) {
	if (($i%3) == 0) {
		echo '
			<tr>';
	}
	echo '
				<td width="33%" align="center" height="100">
					<a href="'.$GLOBALS["SYS_URL_ROOT"].'/'.$show_function[$function_key[$i]].'">
						<img src="'.$GLOBALS["SYS_URL_ROOT"].'/images/'.$function_pic[$function_key[$i]].'" border="0"><br>
						'.$STRING[$function_key[$i]].'
					</a>
				</td>';
	if (($i%3) == 2) {
		echo '
			</tr>';
	}
}
for ($j=0; $j<(3-($i%3)); $j++) {
	echo '
				<td width="33%">&nbsp;</td>';
}
if ($j!=0) {
	echo '
			</tr>';
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
