<?php
$dbhost = $_REQUEST['dbhost'];
$uname  = $_REQUEST['uname'];
$pwd	= $_REQUEST['pwd'];
$dbname	= $_REQUEST['dbname'];
if($_GET['action']=="chkdb"){
	$con = @mysql_connect($dbhost,$uname,$pwd);
	if (!$con){
		die('-1');
	}
	$rs = mysql_query('show databases;');
	while($row = mysql_fetch_assoc($rs)){
		$data[] = $row['Database'];
	}
	unset($rs, $row);
	mysql_close();
	if (in_array(strtolower($dbname), $data)){
		echo '1';
	}else{
	   echo '0';
	}
}elseif($_GET['action']=="creatdb"){
	if(!$dbname){
		die('0');
	}
	$con = @mysql_connect($dbhost,$uname,$pwd);
	if (!$con){
		die('-1');
	}
	if (mysql_query("CREATE DATABASE {$dbname} DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci",$con)){
	  echo "1";
	}else{
	  echo mysql_error();
	}
	mysql_close($con);
}
exit;
?>