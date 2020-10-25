<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: system_about.php,v 1.6 2008/12/09 02:34:05 alex Exp $
 *
 */
include("../include/header.php");

AuthCheckAndLogin();

?>	
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> /
	<a href="system.php?page=information"><?php echo $STRING['title_information']?></a> /
	<?php echo $STRING['system_about']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/outline_about.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['system_about'];?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="system.php?page=information"><img src="<?php echo $GLOBALS["SYS_URL_ROOT"]?>/images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			
			<table class="table-main-list" align="center">

			<tr>
				<td width="100%">
<?php
if (file_exists("about/about_".$language.".htm")) {
	include("about/about_".$language.".htm");
} else {
	include("about/about_enu.htm");
}
?>
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
