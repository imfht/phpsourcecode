<?php #该脚本用于连接数据库
define('SAE_MYSQL_HOST_M','localhost');
define('SAE_MYSQL_USER','webgrid');
define('SAE_MYSQL_PASS','2GsDnmPJsuVG4GGs');
define('SAE_MYSQL_DB','webgrid2');
define('SAE_MYSQL_PORT','3306');

$dbc = @mysqli_connect(SAE_MYSQL_HOST_M,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB,SAE_MYSQL_PORT)
	OR die('Could not connected to MySQL: '.mysqli_connect_error());
mysqli_query($dbc,'SET NAMES utf8');

?>
