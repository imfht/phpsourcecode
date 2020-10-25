<?php
/**
 * @CopyRight  (C)2008-2016 LingQiFei Development team Inc.
 * @WebSite    www.07fly.com www.07fly.top
 * @Author     Liangjing.org <asp3721@hotmail.com>
 * @Brief      liangjingcms v1.x
**/
error_reporting(E_ALL & ~E_NOTICE);
require_once '../Crm/Config/version.inc.php';
$rootpath = '../';
$selfurl = $_SERVER['PHP_SELF'];
$installroot = str_replace("install/index.php","",$selfurl);
$installroot = str_replace("install/","",$installroot);

/* 函数 */
function IsName($name){
	$entities_match		= array(',',';','$','!','@','#','%','^','&','*','_','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',"'",'.','/','*','+','~','`','=');
	for ($i = 0; $i<count($entities_match); $i++) {
	     if(strpos($name, $entities_match[$i])){
               return false;
		 }
	}
   return true;
}

function IsPass($pass){
	return preg_match("/^[[:alnum:]]+$/i", $pass);
}

function PassGen($length = 8){
	$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0, $passwd = ''; $i < $length; $i++)
		$passwd .= substr($str, mt_rand(0, strlen($str) - 1), 1);
	return $passwd;
}

function DB_Query($sql){
	global $footer;

	$result = MYSQL_QUERY ($sql);
	if(!$result){
		$message  = "数据库访问错误\r\n\r\n";
		$message .= $sql . " \r\n";
		$message .= "错误内容: ". mysql_error() ." \r\n";
		$message .= "错误代码: " . mysql_errno() . " \r\n";
		$message .= "时间: ".gmdate('Y-m-d H:i:s', time() + (3600 * 8)). "\r\n";
		$message .= "文件: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

		echo '<center><font class=ohredb><b>数据库访问错误!</b></font><br /><p><textarea rows="28" style="width:460px;">'.htmlspecialchars($message).'</textarea></p>
		<input type="button" name="back" value=" 返&nbsp;回 " onclick="history.back();return false;" />		
		</center><BR>';
		echo $footer;
		exit();
	}else{
		return true;
	}
}


echo "<html>
<head>
<title>".FLYCRM_VERSION." - 安装向导</title>
<link rel=\"stylesheet\" href=\"styles.css\" />
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
</head>
<body>
<table width=\"480\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" align=\"center\" class=\"box\">
<tr>
<td class=\"title\">07FLY-CRM客户管理系统".FLYCRM_VERSION." - 安装向导</td>
</tr>
<tr>
<td valign=\"top\" style=\"padding: 5px;\">";

$footer = '</td></tr></table></body></html>';

/* 判断是否安装 */
@include($rootpath . 'source/Config/Config.inc.php');
if(defined('FLYCRM')){
	echo "<font class=ohredb><b>07FLY-CRM客户管理系统已经安装!</b></font><BR><BR>
	如果您希望重新安装，请先删除source/Config/Config.inc.php文件的 <br />define('FLYCRM',true);<br /><br /><a href='../index.php'>返回首页</a>";
	echo $footer;
	exit();
}

/* 获取参数 */
$servername      = isset($_POST['install']) ? trim($_POST['servername'])		: 'localhost';
$dbname          = isset($_POST['install']) ? trim($_POST['dbname'])            : '';
$dbusername      = isset($_POST['install']) ? trim($_POST['dbusername'])        : '';
$dbpassword      = isset($_POST['install']) ? trim($_POST['dbpassword'])        : '';
$tableprefix     = isset($_POST['install']) ? trim($_POST['tableprefix'])       : 'fly_';//表的前缀
$confirmprefix   = isset($_POST['install']) ? trim($_POST['confirmprefix'])	    : '';//判断表是否存在

$username        = isset($_POST['install']) ? trim($_POST['username'])			: '';//帐号密码
$password        = isset($_POST['install']) ? trim($_POST['password'])			: '';
$confirmpassword = isset($_POST['install'])? trim($_POST['confirmpassword'])	: '';

$sitename        = isset($_POST['install']) ? trim($_POST['sitename'])			: '';
$tableprefix_err = 0;


/*  执行安装  */
if(isset($_POST['install'])){

	/* 判断是否有可写权限 */
	@chmod($rootpath.'Crm/Config/', 0777);

	if (!is_writable($rootpath.'Crm/Config/'))
		$installerrors[] = '请将Crm/Config/文件夹的属性设置为: 777';

	if(!is_writeable($rootpath.'Crm/Config/Config.php')) {
		$installerrors[] = '请将系统配置文件Crm/Config/Config.php设置为可写, 即属性设置为: 777';
	}

//	if(!is_writeable($rootpath.'source/Config/Config.inc.php')) {
//		$installerrors[] = '请将系统配置文件source/conf/config.inc.php设置为可写, 即属性设置为: 777';
//	}

/*	if(strlen($username) == 0){
		$installerrors[] = '请输入系统管理用户名.';
	}else if(!IsName($username)){
		$installerrors[] = '用户名中含有非法字符.';
	}

	if(strlen($password) == 0){
		$installerrors[] = '请输入系统管理密码.';
	}else if(!IsPass($password)){
		$installerrors[] = '密码中含有非法字符.';
	}

	if($password != $confirmpassword)
		$installerrors[] = '管理密码与确认密码不匹配.';
*/
/*	if(strlen($tableprefix) == 0){
		$installerrors[] = '请输入数据库表前缀.';
	}else if(!preg_match('/^[A-Za-z0-9]+_$/', $tableprefix)){
		$installerrors[] = '数据库表前缀只能是英文字母或数字, 而且必需以 _ 结尾.';
	}*/


	// Determine if MySql is installed
	if(function_exists('mysql_connect')){
		// attempt to connect to the database
		if($connection = @MYSQL_CONNECT($servername, $dbusername, $dbpassword)){

			$sqlversion = @mysql_get_server_info();
			if(empty($sqlversion)) $sqlversion='5.0';

			if($sqlversion >= '4.1'){
				mysql_query("set names 'utf8'");
				mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
				mysql_query("ALTER DATABASE $dbname DEFAULT CHARACTER SET utf8 COLLATE 'utf8_general_ci'");           
			}

			if($sqlversion >= '5.0'){
				mysql_query("SET sql_mode=''");
			}

			// connected, now lets select the database
			if($dbname){
				if(!@MYSQL_SELECT_DB($dbname, $connection)){
					// The database does not exist... try to create it:
					if(!@DB_Query("CREATE DATABASE $dbname")){
						$installerrors[] = '创建数据库 "' . $dbname . '" 失败! 您的用户名可能没有创建数据库的权限.<br />' . mysql_error();
					}else{
						if($sqlversion >= '4.1'){
							mysql_query("set names 'utf8'");
							mysql_query("SET COLLATION_CONNECTION='utf8_general_ci'");
							mysql_query("ALTER DATABASE $dbname DEFAULT CHARACTER SET utf8 COLLATE 'utf8_general_ci'");
						}

						if($sqlversion >= '5.0'){
							mysql_query("SET sql_mode=''");
						}
						// Success! Database created
						MYSQL_SELECT_DB($dbname, $connection);
					}
				}
			}else{
				$installerrors[] = '请输入数据库名称.';
			}
		}else{
			// could not connect
			$installerrors[] = '无法连接MySql数据库服务器, 信息:<br />' . mysql_error();
		}
	}else{
		// mysql extensions not installed
		$installerrors[] = '网站服务器环境不支持MySql数据库.';
	}
    

	if(!isset($installerrors)){
		$SqlLines = @file('crm.sql');
		if (!$SqlLines) {
			$installerrors[] = '无法加载数据文件: install/crm.sql';
		} else {
			if(!$confirmprefix) {
				if($query = mysql_query("SHOW TABLES FROM $dbname")) {
					while($row = mysql_fetch_row($query)) {
						if(preg_match("/^$tableprefix/", $row[0])) {
							$tableprefix_err = 1;
							break;
						}
					}
				}
			}
			if(!$tableprefix_err){
				$CurrentQuery = '';
				$CurrentLine = '';
				for ($i = 0; $i < count($SqlLines); $i++) {
					$CurrentLine = trim($SqlLines[$i]);
					
					if (substr($CurrentLine, 0, 2) == '--' || substr($CurrentLine, 0, 3) == '/*!' || $CurrentLine == '') continue;
					
					$CurrentQuery .= $CurrentLine;
					
					if (substr(trim($CurrentLine), -1, 1) == ';'){
						$CurrentQuery = str_replace("ljcms_",$tableprefix,$CurrentQuery);
						//echo $CurrentQuery;
						//echo "<hr>";
						DB_Query($CurrentQuery);
						$CurrentQuery = '';						
					}
				}
				
				//写入数据帐号密码
				//DB_Query ("INSERT INTO " . $tableprefix . "admin VALUES (1, '$username', '".md5($password)."', 0, 1, ".time().", 1, 0, 0, '', '') ");

				/* 生成配置文件 */
				$config_contents="<?php
/**
 * @CopyRight  (C)2006-2017 07fly Development team Inc.
 * @WebSite    www.07fly.com www.07fly.top
 * @Author     07fly.com <web@07fly.com>
 * @Brief      07flyCRM v1.x
 * @Update     2016.06.11
 * @author:    kfrs
**/
//用户配置
 return array (

	'URLMode'   => 0,			
	'ActionDir' => 'hiddenDir/',
	'htmlExt'   => '.html',
	'ReWrite'   => false,
	'Debug'     => false,  
	'Session'   => true,
	'pageSize'  =>20,
	'xml'=>array(
		'path'=>EXTEND.'xml',
		'root'=>'niaomuniao',
	),	
	'DB'=>array(
	'Persistent'=>false,
	'DBtype'    => 'Mysql',
	'DBcharSet' => 'utf8',
	'DBhost'    => '".$servername."',
	'DBport'    => '3306',
	'DBuser'    => '".$dbusername."',
	'DBpsw'     => '".$dbpassword."',
	'DBname'    => '".$dbname."'
	),
	
	'setSmarty'=>array(
		'template_dir'    => VIEW.'templates',
		'compile_dir'     => _mkdir(CACHE. 'templates_c'),
		'left_delimiter'  => '#{',
		'right_delimiter' => '}#',
	),
); 
?>";
				$configfilenum = fopen ($rootpath . "/Crm/Config/Config.php","w");
				ftruncate($configfilenum, 0);
				fwrite($configfilenum, $config_contents);
				fclose($configfilenum);


				echo '<font class=ohblueb>恭喜: 07FLY-CRM客户管理系统 安装成功!</font><br /><br />请在删除07FLY-CRM安装目录(./install/)后继续!
					<br /><br />
					1)、<a href="' . $rootpath . '/index.php" target="_blank"><b>点击进入系统登录页面!</b></a>
					<br /><br />
					';
			}
		}
	}
}

if(!isset($_POST['install']) OR isset($installerrors) OR $tableprefix_err){
	if(isset($installerrors)){//安装出错时
		echo '<table width="97%" border="0" cellpadding="5" cellspacing="0" align="center">
		<tr>
		<td style="border: 1px solid #FF0000; font-size: 12px;" bgcolor="#FFE1E1">
		<u><b>安装错误!</b></u><br /><br />
		安装过程中发现以下错误:<br />';

		for($i = 0; $i < count($installerrors); $i++){
			echo '<b>' . ($i + 1) . ') ' . $installerrors[$i] . '</b><br />';
		}
		echo '</td></tr></table><br />';
	}

	echo '<table width="96%" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
	<td valign="top" align="right"><u>07FLYCRM. 简体中文版(utf-8)</u></td>
	</tr>  
	</table>
	<br />
	<b>1) 填写07FLYCRM数据库连接信息：</b><br /><br />
	<form method="post" action="index.php" name="installform">
	<table width="92%" border="0" cellpadding="0" cellspacing="0" align="center" class="maintable">
	<tr>
	<td valign="middle">数据库服务器地址：</td>
	<td valign="middle" align="right"><input type="text" name="servername" value="' . $servername . '" /></td>
	</tr>
	<tr>
	<td valign="middle">数据库名：</td>
	<td valign="middle" align="right"><input type="text" name="dbname" value="' . $dbname . '" /></td>
	</tr>
	<tr>
	<td valign="middle">数据库用户名：</td>
	<td valign="middle" align="right"><input type="text" name="dbusername" value="' . $dbusername . '" /></td>
	</tr>
	<tr>
	<td valign="middle">数据库密码：</td>
	<td valign="middle" align="right"><input type="text" name="dbpassword" value="' . $dbpassword . '" /></td>
	</tr>
	<!--<tr valign="middle">
	<td valign="middle">数据库表前缀：</td>
	<td valign="middle" align="right"><input type="text" name="tableprefix" value="' . $tableprefix . '" /></td>
	</tr>-->';

	if($tableprefix_err OR $confirmprefix){
		echo '<tr>
		<td valign="middle"><font class=ohredb><B>强制安装:</B><BR>当前数据库当中已经含有相同表前缀的数据表, 您可以重填"表前缀"来避免删除旧的数据, 或者选择强制安装。强制安装将删除原有相同表前缀的数据库表, 且无法恢复!</font></td>
		<td valign="middle"><input type="checkbox" name="confirmprefix" value="1"' . ($confirmprefix ? ' checked="checked"' : ''). ' /> 删除数据, 强制安装 !!!</td>
		</tr>';
	}

	echo '</table>
	<br /><br />
	<b>2) 创建07FLYCRM系统管理帐号：</b><br /><br />
	<table width="92%" border="0" cellpadding="0" cellspacing="0" align="center" class="maintable">
	<tr>
	<td valign="middle">用户名：</td>
	<td valign="middle" align="right"><input type="text" name="username" value="admin" readonly /></td>
	</tr>
	<tr>
	<td valign="middle">登录密码：</td>
	<td valign="middle" align="right"><input type="text" name="password" value="admin123456" readonly /></td>
	</tr>
	<tr>
	<td valign="middle">确认密码：</td>
	<td valign="middle" align="right"><input type="text" name="confirmpassword" value="admin123456" readonly/></td>
	</tr>
	</table>
	<br /><br /><center><input type="submit" name="install" value="安装 07FLYCRM" /></center>
	</form><script type="text/JavaScript">document.getElementById("installform").dbname.focus();</script>';
}
echo $footer;
?>