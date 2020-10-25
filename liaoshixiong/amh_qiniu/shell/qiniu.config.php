<?php
	require_once	'/home/wwwroot/index/web/Amysql/Config.php';
	$con = mysql_connect($Config['Host'],$Config['User'],$Config['Password']);
	if (!$con)
	  {
	  die('Could not connect: ' . mysql_error());
	  }
	mysql_select_db($Config['DBname'], $con);

	//从数据库里获取配置文件
	$sql = "SELECT * FROM amh_backup_remote where remote_type = 'ALiOSS' limit 1";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result)){
		$accessKey = $row['remote_user'];
		$secretKey = $row['remote_password'];
		$bucket = $row['remote_path'];
		$domain = $row['remote_ip'];
	}

?>