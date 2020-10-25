<?php
/* Session holder, handling users sessions in all apps
 * v0.1
 * wadelau@ufqi.com
 * Sat Jul 23 09:50:58 UTC 2011
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/inc/socket.class.php");
#require_once(__ROOT__."/inc/class.connectionpool.php");

 class Session{
	 
 	//- construct
	function __construct(){
		//-
	}

	# @Todo: to be implemented when need 2nd standalone appserver

 	
 }
?>
