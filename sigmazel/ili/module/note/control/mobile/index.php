<?php
//版权所有(C) 2014 www.ilinei.com
if(!defined('INIT')) exit('Access Denied');

require_once ROOTPATH.'/module/note/lang/mobile.php';

$nav_title = $GLOBALS['lang']['note'];

$notes = note_get_list("AND a.DISPLAYORDER = 1");
if(count($notes) == 0) mobile_show_message($GLOBALS['lang']['error.module.disabled']);

$note = $notes[0];

if($_var['gp_formsubmit']){
	if(empty($_var['gp_txtUserName'])) mobile_show_message($GLOBALS['lang']['note.error.username']);
	elseif($note['NEEDS']['email'] && empty($_var['gp_txtEmail'])) mobile_show_message($GLOBALS['lang']['note.error.email']);
	elseif($note['NEEDS']['connect'] && empty($_var['gp_txtConnect'])) mobile_show_message($GLOBALS['lang']['note.error.connect']);
	elseif(empty($_var['gp_txtContent'])) mobile_show_message($GLOBALS['lang']['note.error.content']);
	
	$noteid = record_insert(array(
	'NOTEID' => $note['NOTEID'], 
	'TITLE' => cutstr(strip_tags($_var['gp_txtContent']), 35),
	'DEPARTMENT' => utf8substr($_var['gp_txtDepartment'], 0, 50), 
	'PLACE' => utf8substr($_var['gp_txtPlace'], 0, 50), 
	'EMAIL' => utf8substr($_var['gp_txtEmail'], 0, 30), 
	'CONNECT' => utf8substr($_var['gp_txtConnect'], 0, 30), 
	'CONTENT' => utf8substr(strip_tags($_var['gp_txtContent']), 0, 100), 
	'USERID' => $_var['current']['USERID'],
	'USERNAME' => utf8substr($_var['gp_txtUserName'], 0, 20), 
	'EDITTIME' => date('Y-m-d H:i:s')
	));
	
	mobile_show_message($GLOBALS['lang']['note.submit.success'], "mobile.do?ac=note");
}

include_once view('/module/note/theme/mobile/index');
?>