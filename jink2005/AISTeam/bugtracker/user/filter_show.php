<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: filter_show.php,v 1.9 2013/07/05 22:40:18 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

if (!isset($_GET['filter_id']) ){
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "filter_id");
}
$getfilter_sql = "select * from ".$GLOBALS['BR_filter_table']." 
		where filter_id=".$GLOBALS['connection']->QMagic($_GET['filter_id'])." and 
		user_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'uid']);
$getfilter_result = $GLOBALS['connection']->Execute($getfilter_sql) or DBError(__FILE__.":".__LINE__);
$filter_line = $getfilter_result->Recordcount();

// �p�G�S����o�ߤ@�� filter�A�h��ܿ�~�����}
if ($filter_line != 1) {
	WriteSyslog("error", "syslog_not_found", "filter", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "filter");
}
$filter_name = $getfilter_result->fields["filter_name"];
$text_condition = $getfilter_result->fields["text_condition"];
$share_filter = $getfilter_result->fields["share_filter"];

?>

<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="../system/system.php"><?php echo $STRING['title_system']?></a> /
	<a href="filter_setting.php"><?php echo $STRING['set_filter']?></a> /
	<?php echo $STRING['show_filter']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_filter.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['show_filter']?></tt>
			</td>
			<td nowrap valign="bottom" width="100%" align="right">
				<a href="filter_setting.php"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

			<h3>&nbsp;</h3>

			<table class="table-input-form">
			<tr>
				<td width="150" class="item_prompt_small">
					<?php echo $STRING['filter_name'].$STRING['colon']?>
				</td>
				<td width="350">
					<?php echo $filter_name?>
				</td>
			</tr>
			<tr>
				<td class="item_prompt_small">
					<?php echo $STRING['share_filter'].$STRING['colon']?>
				</td>
				<td>
<?php
if ($share_filter == 't') {
	echo $STRING['yes'];
} else {
	echo $STRING['no'];
}
?>
				</td>
			</tr>
			<tr>
				<td class="item_prompt_small prompt_align_top">
					<?php echo $STRING['filter']?>
				</td>
				<td>
					<?php echo $text_condition?>
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
