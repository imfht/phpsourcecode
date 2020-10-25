<?php	header('Content-Type:text/html; charset=utf-8');

/*
**************************
(C)2015 xingcms.cn
**************************
*/


//检测版本号
if(phpversion() < '5.0')
{
	exit('用户您好，由于您的php版本过低，不能安装本软件，为了系统功能全面可用，请升级到5.0或更高版本再安装，谢谢！<br />您可以登录 xingcms.cn 获取更多帮助');
}


//不限制响应时间
error_reporting(0);
set_time_limit(0);


//设置系统路径
define('INSTALL_PATH', preg_replace('/[\/\\\\]{1,}/', '/', dirname(__FILE__)));
define('IN_INSTALL', TRUE);


//版权信息设置
$cfg_copyright = '© 2015 XINGCMS.CN';


//提示已经安装
if(is_file(INSTALL_PATH.'/install_lock.txt') && $_GET['s']!=md5('done'))
{
	require_once(INSTALL_PATH.'/templates/step_5.html');
	exit();
}


//初始化参数
$s = !empty($_POST['s']) ? intval($_POST['s']) : 0;


//如果有GET值则覆盖POST值
if(!empty($_GET['s']))
{
	if($_GET['s']==1 or $_GET['s']==15271 or $_GET['s']==md5('done'))
	{
		$s = $_GET['s'];
	}
}


//执行相应操作
//协议说明
if($s == 0)
{
	require_once(INSTALL_PATH.'/templates/step_0.html'); 
	exit();
}


//环境检测
else if($s == 1)
{
	$iswrite_array = array('/cache/',
						   '/news/',
						   '/config.php',
						   '/upload/',
						   '/templates_c/',
						   '/buycars/');

	$exists_array = array('is_writable',
						  'function_exists',
						  'mysql_connect');

	require_once(INSTALL_PATH.'/templates/step_1.html');
	
	exit();
}


//配置文件
else if($s == 2)
{
	require_once(INSTALL_PATH.'/templates/step_2.html');
	exit();
}


//正在安装
else if($s == 3)
{
	require_once(INSTALL_PATH.'/templates/step_3.html');


	if($_POST['s'] == 3)
	{

		//初始化信息
		$dbhost = isset($_POST['dbhost']) ? $_POST['dbhost'] : '';
		$dbname = isset($_POST['dbname']) ? $_POST['dbname'] : '';
		$dbuser = isset($_POST['dbuser']) ? $_POST['dbuser'] : '';
		$dbpwd  = isset($_POST['dbpwd'])  ? $_POST['dbpwd']  : '';
		$tbpre  = isset($_POST['tbpre'])  ? $_POST['tbpre']  : '';

		$username   = isset($_POST['username'])   ? $_POST['username']   : '';
		$password   = isset($_POST['password'])   ? $_POST['password']   : '';
		$repassword = isset($_POST['repassword']) ? $_POST['repassword'] : '';
		$testdata   = isset($_POST['testdata'])   ? $_POST['testdata']   : '';


		//验证数据库
		$conn = mysql_connect($dbhost, $dbuser, $dbpwd);
		if($conn)
		{
			if(mysql_get_server_info() < '4.0')
			{
				echo '<script>$("#install").append("检测到您的数据库版本过低，请更新！");</script>';
				exit();
			}


			//查询数据库
			$res = mysql_query('show Databases');


			//遍历所有数据库，存入数组
			while($row = mysql_fetch_array($res))
			{
				$dbname_arr[] = $row['Database'];
			}


			//检查数据库是否存在，没有则创建数据库
			if(!in_array(trim($dbname), $dbname_arr))
			{
				if(!mysql_query("CREATE DATABASE `".$dbname."`"))
				{
					echo '<script>$("#install").append("创建数据库失败，请检查权限或联系管理员！");</script>';
					exit();
				}
			}


			//数据库创建完成，开始连接
			mysql_select_db($dbname, $conn);


			//取出conn.inc模板内容
			$config_str = '';
			$fp = fopen(INSTALL_PATH.'/data/conn.tpl.php', 'r');
			while(!feof($fp))
			{
				$config_str .= fgets($fp, 1024);
			}
			fclose($fp);


			//进行替换
			$config_str = str_replace('~db_host~', $dbhost, $config_str);
			$config_str = str_replace('~db_name~', $dbname, $config_str);
			$config_str = str_replace('~db_user~', $dbuser, $config_str);
			$config_str = str_replace('~db_pwd~',  $dbpwd,  $config_str);
			$config_str = str_replace('~db_tablepre~', $tbpre, $config_str);
			$config_str = str_replace('~db_charset~', 'utf8', $config_str);


			//将替换后的内容写入conn.inc文件
			$fp = fopen(INSTALL_PATH.'/../config.php', 'w');
			fwrite($fp, $config_str);
			fclose($fp);


			//防止浏览器缓存
			$buffer = ini_get('output_buffering');
			echo str_repeat(' ', $buffer + 1);


			echo '<script>$("#install").append("数据库连接文件创建完成！<br />");</script>';
			ob_flush();
			flush();

			//设置数据库状态
			mysql_query("SET NAMES 'utf8', character_set_client=binary, sql_mode='', interactive_timeout=3600;");


			//创建表结构
			$tbstruct = '';
			$fp = fopen(INSTALL_PATH.'/data/install_struct.txt', 'r');
			while(!feof($fp))
			{
				$tbstruct .= fgets($fp, 1024);
			}
			fclose($fp);


			$querys = explode(';', ClearBOM($tbstruct));
			foreach($querys as $q)
			{
				if(trim($q) == '') continue;
				mysql_query(str_replace('sss_', $tbpre, trim($q)).';');
			}

			echo '<script>$("#install").append("数据库结构导入完成！<br />");</script>';
			ob_flush();
			flush();


			


			//创建管理员
			mysql_query("INSERT INTO `".$tbpre."admin` VALUES('1', '".$username."', '".md5($password)."', '0', 'administrator', '1431651896', '127.0.0.1', '409', '1', '', '');");

			echo '<script>$("#install").append("管理员信息导入完成！<br />");</script>';
			ob_flush();
			flush();


			//初始化环境变量
			if(!empty($_SERVER['REQUEST_URI']))
				$scriptName = $_SERVER['REQUEST_URI'];
			else
				$scriptName = $_SERVER['PHP_SELF'];

			$basepath = preg_replace("#\/install(.*)$#i", '', $scriptName);

			if(!empty($_SERVER['HTTP_HOST']))
				$baseurl = 'http://'.$_SERVER['HTTP_HOST'];
			else
				$baseurl = 'http://'.$_SERVER['SERVER_NAME'];
				
			$authkey = GetRandStr(16);




		


		


			//查看是否需要安装测试数据
			if($testdata == 'true')
			{
				echo '<script>$("#install").append("正在加载测试数据！<br />");</script>';
				ob_flush();
				flush();

				$sqlstr_file = INSTALL_PATH.'/data/install_testdata.txt';
				if(filesize($sqlstr_file) > 0)
				{
					$fp = fopen($sqlstr_file, 'r');
					while(!feof($fp))
					{
						$line = trim(fgets($fp, 512*1024));
						if($line == '') continue;
						mysql_query(str_replace('sss_', $tbpre, trim($line)));
					}
					fclose($fp);
				}
	
				echo '<script>$("#install").append("测试数据导入完成！");</script>';
				ob_flush();
				flush();

			}
			
			
			//结束缓存区
			ob_end_flush();


			//安装完成进行跳转
			echo '<script>location.href="?s='.md5('done').'";</script>';
			exit();
		}
		else
		{
			echo '<script>$("#install").append("数据库连接错误，请检查！");</script>';
			exit();
		}
	}
	
	exit();
}


//检测数据库信息
else if($s == 15271)
{

	$dbhost = isset($_GET['dbhost']) ? $_GET['dbhost'] : '';
	$dbuser = isset($_GET['dbuser']) ? $_GET['dbuser'] : '';
	$dbpwd  = isset($_GET['dbpwd'])  ? $_GET['dbpwd']  : '';

	if(mysql_connect($dbhost, $dbuser, $dbpwd))
		echo 'true';
	else
		echo 'false';

	exit();
}


//安装完成
else if($s == md5('done'))
{
	require_once(INSTALL_PATH.'/templates/step_4.html');

	$fp = fopen(INSTALL_PATH.'/install_lock.txt', 'w');
	fwrite($fp, '程序已正确安装，重新安装请删除本文件');
	fclose($fp);

	exit();
}


//协议说明
else
{
	require_once(INSTALL_PATH.'/templates/step_0.html');
	exit();
}


//测试可写性
function IsWrite($file)
{
	if(is_writable($file))
	{
		echo '可写';
		$GLOBALS['isnext'] = 'Y';
	}
	else
	{
		echo '不可写';
		$GLOBALS['isnext'] = 'N';
	}
}


//测试函数是否存在
function IsFunExists($func)
{
	if(function_exists($func))
	{
		echo '支持';
		$GLOBALS['isnext'] = 'Y';
	}
	else
	{
		echo '不支持';
		$GLOBALS['isnext'] = 'N';
	}
}


//测试函数是否存在
function IsFunExistsTxt($func)
{
	if(function_exists($func))
	{
		echo '无';
		$GLOBALS['isnext'] = 'Y';
	}
	else
	{
		echo '建议安装';
		$GLOBALS['isnext'] = 'N';
	}
}


//清除txt中的BOM
function ClearBOM($contents)
{
	$charset[1] = substr($contents, 0, 1);
	$charset[2] = substr($contents, 1, 1);
	$charset[3] = substr($contents, 2, 1);

	if(ord($charset[1])==239 &&
	   ord($charset[2])==187 &&
	   ord($charset[3])==191)
	{
		return substr($contents, 3);
	}
	else
	{
		return $contents;
	}
}


//产生随机字符串
function GetRandStr($length=6)
{
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$random_str = '';

	for($i=0; $i<$length; $i++)
	{
		$random_str .= $chars[mt_rand(0, strlen($chars) - 1)];
	}

	return $random_str;
}
?>