<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */
 
session_start();
require_once (dirname(__FILE__) . "/config.inc.php");
include(PJINC.'/core/filesys.lib.php');

$nf_d = new filesys;
if(isset($_POST['put'])){
	if($_POST['put']!=''){
		$show = '';
		$host = trim($_POST['host']);
		$name = trim($_POST['name']);
		$user = trim($_POST['user']);
		$pwd = trim($_POST['pwd']);
		$prefix = trim($_POST['prefix']);
		$data = array();
		$data['webname'] = trim($_POST['webname']);
		$data['hostname'] = trim($_POST['hostname']);
		$data['mail'] = trim($_POST['mail']);
		$data['adminuser'] = trim($_POST['adminuser']);
		$data['adminpwd'] = trim($_POST['adminpwd']);
		$conn = mysql_connect($host,$user,$pwd);
		if(!$conn){
			$show = mysql_error();
			echo '<script>setTimeout("window.location=\'../error.php?show='.urlencode($show).'\'",0);</script>';
			exit();
		}
		if(!mysql_select_db($name,$conn)){
			$db = execute("CREATE DATABASE `".$name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci",$conn);
			if(!$db){
				$show = mysql_error();
				echo '<script>setTimeout("window.location=\'../error.php?show='.urlencode($show).'\'",0);</script>';
				exit();
			}
		}
		
		
		$url = PJDATA;
		$file =$nf_d->nListPath($url);
		$info['data'] = $data;
		$info['file'] = $file;
		$datas = json_encode($info);
		file_put_contents(RUNTIME.'/data.php',$datas);
		file_put_contents(RUNTIME.'/data_copy.php',"<"."?php\r\n \$info_copy = ".var_export($info,true).';');
		//$_SESSION['mail'] = $data['mail'];
		//$_SESSION['info'] = serialize($info);
		//print_r($_SESSION['info']);exit;
		if(!is_writeable(CONF)){
			show_msg(0,'Conf目录不可写');
		}
		$fp = fopen(CONF.'/conn.php','wb');
		flock($fp,3);
		fwrite($fp,"<"."?php\r\n//数据库配置信息\r\n");
		fwrite($fp,"return array(\r\n");
		fwrite($fp,"\t'DB_TYPE' => 'mysql', // 数据库类型\r\n");
		fwrite($fp,"\t'DB_HOST' => '".$host."', // 服务器地址\r\n");
		fwrite($fp,"\t'DB_NAME' => '".$name."', // 数据库名\r\n");
		fwrite($fp,"\t'DB_USER' => '".$user."', // 用户名\r\n");
		fwrite($fp,"\t'DB_PWD' => '".$pwd."', // 密码\r\n");
		fwrite($fp,"\t'DB_PORT' => 3306, // 端口\r\n");
		fwrite($fp,"\t'DB_PREFIX' => '".$prefix."' // 数据库表前缀,\r\n");
		fwrite($fp,");");
		echo '<script>setTimeout("window.location=\'../putdata.php?mail='.$data['mail'].'&total='.count($info['file']).'\'",0);</script>';
		//mysql_close($conn);
		exit();
	}
}

//sql函数
function execute($sql,$conn){
	$rs = mysql_query($sql,$conn);
	if(!$rs){
		return mysql_error();
		exit();
	}else{
		return 1;
	}
	mysql_free_result($rs);
}