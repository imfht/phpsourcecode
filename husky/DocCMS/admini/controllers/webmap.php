<?php 
function index()
{
	global $db,$request,$webMap;
	$sql = "SELECT * FROM `".TB_PREFIX."menu` WHERE deep='0' AND isHidden='0'";
	$webMap = $db->get_results($sql);
}
function edit()
{
}
?>