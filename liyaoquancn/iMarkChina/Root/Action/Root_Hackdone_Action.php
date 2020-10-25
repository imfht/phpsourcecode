<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
define(login,'../');
include login.'Index/Point/Index_Config_Action.php';
if (!isset($_SESSION['Mark_Login'])) {
		if (@$_GET['login'] == 'login') {
		$u = $Mark_Config_Action['site_link'];
		$l = $Mark_Config_Action['level'];
		header("location:".$u.$l."/root.php");
	}elseif(@$_GET['login'] == 'out'){
		$uu = $Mark_Config_Action['site_link'];
		$ll = $Mark_Config_Action['level'];
		header("location:".$uu.$ll."/");
	}
    echo "<title>How crazy is it?</title>";
    echo "<meta charset=\"UTF-8\" />";
    echo "<style type=\"text/css\">
a {text-decoration:none;color:#426DC9;}
#shit {font-size: 100px;margin-top: 150px;}
#fontt {	font-family: Arial, Helvetica, sans-serif;	font-size: 18px;}</style>";
echo "<div id=\"shit\">
⊙﹏⊙‖∣°
</div>
<br />
  <br />
<span id=\"fontt\"> 
Oh,Shit !!! Where are they go ?.<br /><br />
One last thing?.<br /><br />
Are you very sure you're the Root?.<br /><br />
Are You....????.....<br /><br />
<a href=\"index.php?login=login\">Yes,I'm.</a>&nbsp;&nbsp;&nbsp;<a href=\"index.php?login=out\"> No,I change my mind.</a>
</span>";
die;
}
if (@$_GET['getout'] == 'logout') {
    session_start();
    session_destroy();
    $root_file = $Mark_Config_Action['root_file'];
    $level = $Mark_Config_Action['level'];
    header("location:".$level."/".$root_file);
}
?>