<?php
error_reporting(0);
require("../../database.php");
$sql="select * from cms_advertiser_pmb a,cms_user_pmb u where a.t_num=u.t_num and a.status='closed' and u.status='closed'";
$query=mysql_query($sql);
$num=mysql_num_rows($query);
if($num>0){
	while($arr=mysql_fetch_array($query)){
		$sql="delete from cms_advertiser_pmb where t_num=".$arr['t_num']." or r_num=".$arr['t_num'];
		mysql_query($sql);
		$sql="delete from cms_user_pmb where t_num=".$arr['t_num'];
		mysql_query($sql);
	}
}
?>