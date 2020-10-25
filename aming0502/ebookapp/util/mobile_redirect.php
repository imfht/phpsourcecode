<?php

include_once WEB_ROOT.'util/Browser.php';
	
if(!defined('ISMOBILE')){
	$browser = new Browser();
	define('ISMOBILE',$browser->isMobile());
}

/*if(ISMOBILE){
	$path ="";
	if(!empty($_SERVER['REDIRECT_URL'])){
		$path = $_SERVER['REDIRECT_URL'];
	}
	$path = "http://m.kaixinpig.net".$path;
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$path\">";;
	exit;
}*/
?>