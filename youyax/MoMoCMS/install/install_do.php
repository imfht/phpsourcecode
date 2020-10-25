<?php
$_POST=array_map("htmlspecialchars",$_POST);
$_POST=array_map("addslashes",$_POST);
try {
    $db = new PDO("mysql:host=".$_POST['db_host'].";dbname=".$_POST['db_name'], $_POST['db_user'], $_POST['db_psw']); //初始化一个PDO对象
} catch (PDOException $e) {
    echo '<script>alert("无法连接数据库，请检查数据库名是否存在!: ' . $e->getMessage().'")</script>';
    exit;
}
$db1=file_get_contents("../database.php");
$db1=str_replace("{DB_HOST}",$_POST['db_host'],$db1);
$db1=str_replace("{DB_USER}",$_POST['db_user'],$db1);
$db1=str_replace("{DB_PSW}",$_POST['db_psw'],$db1);
$db1=str_replace("{DB_NAME}",$_POST['db_name'],$db1);
$db1=str_replace("{DB_PREFIX}",$_POST['db_prefix'],$db1);
$db1=str_replace("{URL}",$_POST['url'],$db1);
file_put_contents("../database.php",$db1);

$db2=file_get_contents("../admin/database.php");
$db2=str_replace("{DB_HOST}",$_POST['db_host'],$db2);
$db2=str_replace("{DB_USER}",$_POST['db_user'],$db2);
$db2=str_replace("{DB_PSW}",$_POST['db_psw'],$db2);
$db2=str_replace("{DB_NAME}",$_POST['db_name'],$db2);
$db2=str_replace("{DB_PREFIX}",$_POST['db_prefix'],$db2);
$db2=str_replace("{URL}",$_POST['url'],$db2);
file_put_contents("../admin/database.php",$db2);

$dbm=file_get_contents("../m/database.php");
$dbm=str_replace("{DB_HOST}",$_POST['db_host'],$dbm);
$dbm=str_replace("{DB_USER}",$_POST['db_user'],$dbm);
$dbm=str_replace("{DB_PSW}",$_POST['db_psw'],$dbm);
$dbm=str_replace("{DB_NAME}",$_POST['db_name'],$dbm);
$dbm=str_replace("{DB_PREFIX}",$_POST['db_prefix'],$dbm);
$dbm=str_replace("{URL}",$_POST['url'],$dbm);
$dbm=str_replace("{URL_M}",$_POST['url']."/m",$dbm);
file_put_contents("../m/database.php",$dbm);

$db3=file_get_contents("./momocms.sql");
$db3=str_replace("{prefix}",$_POST['db_prefix'],$db3);
file_put_contents("./momocms.sql",$db3);

$sql=file_get_contents("./momocms.sql");
$sql_arr=explode(";\n",$sql);
$db->exec("SET sql_mode=''");
$db->exec("set names utf8");
$sql_arr_count=sizeof($sql_arr);
for($i=1;$i < $sql_arr_count;$i++){
	$db->exec($sql_arr[$i]);
}

$db->exec("insert into ".$_POST['db_prefix']."admin set
					user = '".$_POST['admin']."',
					psw = '".md5($_POST['password'])."',
					isAdmin = 1");
$db=null;
echo '<script>parent.window.location.href="../admin";</script>';
?>