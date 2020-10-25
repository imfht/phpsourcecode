<?php
	include_once "../init.php";
	include_once  WEB_ROOT."util/mysql_class.php";

	if(@$_GET["source"]){
		$source = $_GET["source"];
		$db =  new mysql();
		$sql_select="select * from t_adsence where aviable = 1";
		$query = $db->query($sql_select);
		
		$res="";
		while($row=$db->fetch_row_array($query)){
			$res = $res.$row["text"];
		}
		echo $res;
	}
?>
