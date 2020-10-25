<?php
/***************************************************************************  
 *                               extension.inc
 *                            -------------------                         
 *   begin                : Saturday, Feb 13, 2001 
 *   copyright            : (C) 2001 The phpBB Group        
 *   email                : support@phpbb.com                           
 *                                                          
 *   $Id: extension.inc 2480 2002-04-04 11:52:50Z psotfx $
 *                                                            
 * 
 ***************************************************************************/ 

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

header("Content-Type: text/html; charset=UTF-8");

//
// Change this if your extension is not .php!
//
$phpEx = "php";

$starttime = 0;

?>