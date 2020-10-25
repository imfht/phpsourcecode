<?php
/* Copyright (c) 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: system.php,v 1.6 2008/11/28 10:36:10 alex Exp $
 *
 */
include("include/header.php");

AuthCheckAndLogin();
	
$show_function['my_account'] = "user_edit.php";
$show_function['system_about'] = "system_about.php";
$function_pic['my_account'] = "system_user.png";
$function_pic['system_about'] = "system_about.png";

?>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="images/outline_system.png" width="48" height="48" align="middle" border="0">
				<tt class="outline">
<?php
echo $STRING['title_system'];
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
				<td width="33%" align="center" height="100"><a href="'.$show_function[$function_key[$i]].'">
					<img src="images/'.$function_pic[$function_key[$i]].'" border="0"><br>
					'.$STRING[$function_key[$i]].'</a>
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
include("include/tail.php");
?>
