<?php
ini_set("error_reporting",E_ERROR);
$post = $_POST;
//print_r($post);

function script($content,$url='./'){
	echo "<script>alert('".$content."');location.href='".$url."'</script>";
	
}
$con = mysql_connect($post['db_host'],$post['db_user'],$post['db_pwd']);
if (!$con){
  script('无法链接数据库: ' . mysql_error());
   die;
}
if(mysql_select_db($post['db_name'],$con)){
// 	script('数据库 '.$post['db_name'].' 已存在');
//	die;
}else{
	if(!mysql_query('CREATE DATABASE `'.$post['db_name'].'`',$con)){
	  script('数据库 '.$post['db_name'].' 创建失败');
	  die;
	}
}


mysql_select_db($post['db_name'],$con);
mysql_query("SET NAMES 'utf8'",$con);
$install_sql = file_get_contents('./sql.php');
$install_sql = str_replace('#__',$post['db_prefix'],$install_sql);
$sql = explode(';',$install_sql);

foreach ($sql as $v){
	mysql_query($v,$con);
}
mysql_query('update '.$post['db_prefix'].'admin set username="'.$post['user'].'",password="'.md5(md5($post['pwd'])).'"',$con);




$db_str ="//数据库设定\r\n
define('_DATABASE_HOST','".$post['db_host']."');\r\n
define('_DATABASE_USER','".$post['db_user']."');\r\n
define('_DATABASE_PASSWORD','".$post['db_pwd']."');\r\n
define('_DATABASE_NAME','".$post['db_name']."');\r\n
define('_DATABASE_UT','utf8');\r\n
define('_TABLE_FIRST_NAME','".$post['db_prefix']."');\r\n
define('_DATABASE_OPEN','1'); #是否开启数据库连接  1开启 2关闭
";
$tp = fopen('../include/config/config.php', 'a');
fwrite($tp, $db_str);
fclose($tp);


$tp = fopen('./install.log', 'w');


script('安装成功，点击确认进入后台','../index.php?m=admin&t=login');


?>