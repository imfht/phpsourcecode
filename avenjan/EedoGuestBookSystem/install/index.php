<?php
if (file_exists('../libs/install.lock')){
echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>警告(Warning):你已经安装过该系统，如果想重新系统，请删除目录/libs下的 install.lock 文件，然后再安装。</body></html>';
exit;
}
define('CMS',true);
@set_time_limit(1000);
if(phpversion() <= '5.3.0') set_magic_quotes_runtime(0);
if('5.2.0' > phpversion() ) exit('您的php版本过低，不能安装本软件，请升级到5.2.0或更高版本再安装，谢谢！');
date_default_timezone_set('PRC'); //beijing time
error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=UTF-8');
define('SITEDIR', _dir_path(substr(dirname(__FILE__), 0, -8)));
include_once ("./install.config.php");

$step = intval($_REQUEST['step']);
if(! $step) $step = 1;

if($step == 1){
    include('./install_step/1.htm');
    exit();
}
else if($step == 2){
    $phpv = phpversion();
    $sp_os = PHP_OS;
    $sp_gd = gdversion();
    $sp_server = $_SERVER['SERVER_SOFTWARE'];
    $sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
    $sp_name = $_SERVER['SERVER_NAME'];
    $sp_max_execution_time = ini_get('max_execution_time');
    $sp_allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');
    $sp_gd = ($sp_gd>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_mysql = (function_exists('mysql_connect') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
    $sp_curl = (function_exists('curl_init') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');

    if($sp_mysql=='<font color=red>[×]Off</font>')
    $sp_mysql_err = TRUE;
    else
    $sp_mysql_err = FALSE;

    $sp_testdirs = array(
        '/',
        '/install',
        '/libs'
    );
    include('./install_step/2.htm');
    exit();
}
else if($step == 3){
    if(!empty($_SERVER['REQUEST_URI']))
    $scriptName = $_SERVER['REQUEST_URI'];
    else
    $scriptName = $_SERVER['PHP_SELF'];
    
    $basepath = preg_replace("#\/install(.*)$#i", '', $scriptName);
    
    if(!empty($_SERVER['HTTP_HOST']))
        $baseurl = 'http://'.$_SERVER['HTTP_HOST'];
    else
        $baseurl = "http://".$_SERVER['SERVER_NAME'];
    
    include('./install_step/3.htm');
    exit();
}
else if($step == 4){
    $dbhost = trim($_REQUEST['dbhost']);
    $dbuser = trim($_REQUEST['dbuser']);
    $dbpwd = trim($_REQUEST['dbpwd']);
    $dbname = trim($_REQUEST['dbname']);
    $dblang = trim($_REQUEST['dblang']);
    $body_text = "系统开始安装：<br/>";
    
    $conn = mysql_connect($dbhost,$dbuser,$dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");

    mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."`;",$conn);
    mysql_query("SET NAMES UTF8");
    mysql_select_db($dbname) or die("<script>alert('选择数据库失败，可能是你没权限，请预先创建一个数据库！');history.go(-1);</script>");

    //获得数据库版本信息
    $rs = mysql_query("SELECT VERSION();",$conn);
    $row = mysql_fetch_array($rs);
    $mysqlVersions = explode('.',trim($row[0]));
    $mysqlVersion = $mysqlVersions[0].".".$mysqlVersions[1];
	
    mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';",$conn);
    
    $body_text .= "数据库 $dbname 创建成功；<br/>";
    
    //install.mysql.info.php
    $fp = fopen(dirname(__FILE__)."/install.mysql.info.php","r");
    $configStr1 = fread($fp,filesize(dirname(__FILE__)."/install.mysql.info.php"));
    fclose($fp);
    $dbport='';
    for($i=0;$i<strlen($dbhost);$i++){
        if(is_numeric($dbhost[$i])){
            $dbport.=$dbhost[$i];
        }
    };
    
    $configStr1 = str_replace("~dbhost~",$dbhost,$configStr1);
    $configStr1 = str_replace("~dbport~",$dbport,$configStr1);
    $configStr1 = str_replace("~dbname~",$dbname,$configStr1);
    $configStr1 = str_replace("~dbuser~",$dbuser,$configStr1);
    $configStr1 = str_replace("~dbpwd~",$dbpwd,$configStr1);
    $configStr1 = str_replace("~dblang~",$dblang,$configStr1);

    $fp = fopen(dirname(__FILE__)."/../libs/common.db.php","w+") or die("<script>alert('写入配置失败，请检查/conf目录是否可写入！');history.go(-1);</script>");
    fwrite($fp,$configStr1);
    fclose($fp);
    
    $body_text .= "数据库配置文件创建成功；<br/>";

    
    
    //创建数据表
    $query = '';
    $fp = fopen(dirname(__FILE__).'/table_sql.txt','r');
    while(!feof($fp)){
		$line = rtrim(fgets($fp,1024));
		if(preg_match("#;$#", $line)){
			$query .= $line."\n";
			if($mysqlVersion < 4.1){
				$rs = mysql_query($query,$conn);
			}else{
				if(preg_match('#CREATE#i', $query)){
					$rs = mysql_query(preg_replace("#TYPE=MyISAM#i",$sql4tmp,$query),$conn);
				}else{
					$rs = mysql_query($query,$conn);
				}
			}
			$query='';
		}else if(!preg_match("#^(\/\/|--)#", $line)){
			$query .= $line;
		}
	}
	fclose($fp);
	$body_text .= "基本数据库表创建成功；<br/>";
	
	//导入默认数据
	$query = '';
	$fp = fopen(dirname(__FILE__).'/data_sql.txt','r');
	while(!feof($fp)){
		$line = rtrim(fgets($fp, 1024));
		if(preg_match("#;$#", $line)){
			$query .= $line;
			if($mysqlVersion < 4.1) $rs = mysql_query($query,$conn);
			else $rs = mysql_query(str_replace('#~lang~#',$dblang,$query),$conn);
			$query='';
		} else if(!preg_match("#^(\/\/|--)#", $line)){
			$query .= $line;
		}
	}
	fclose($fp);
	$body_text .= "默认数据更新成功；<br/>";
    //锁住文件
	$lockfilepath="../libs";
	$lockfile=$lockfilepath.'/install.lock';
    $fp =fopen($lockfile,"w") or die("<script>alert('写入配置失败，请检查/libs目录是否可写入！');history.go(-1);</script>");
    fwrite($fp,"ok");//写入OK
    fclose($fp);
    
    $body_text .= "系统安装完成！<br/><br/>";
    $body_text .= "<a href='../?go=master'><button class='layui-btn layui-btn-warm'>网站后台</button></a>  [请进入网站后台进行相关设置]";
    
    include('./install_step/4.htm');
    exit();
}
else if($step == 10){
    header("Pragma:no-cache\r\n");
    header("Cache-Control:no-cache\r\n");
    header("Expires:0\r\n");
    $dbhost = trim($_REQUEST['dbhost']);
	$dbname = trim($_REQUEST['dbname']);
    $dbuser = trim($_REQUEST['dbuser']);
    $dbpwd = trim($_REQUEST['dbpwd']);
    
    $conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
    if($conn)
    {
		if(empty($dbname)){
			echo "<font color='green'>信息正确</font>";
		}else{
			$info = mysql_select_db($dbname,$conn);
			if($info){
				$info="<font color='red'>数据库已经存在，系统将覆盖数据库</font>";
			}else{
				$info="<font color='green'>数据库不存在,系统将自动创建</font>";
			}
			echo $info;
		}
    }
    else
    {
        echo "<font color='red'>数据库连接失败！</font>";
    }
    @mysql_close($conn);
    exit();
}


function _dir_path($path) {
	$path = str_replace('\\', '/', $path);
	if(substr($path, -1) != '/') $path = $path.'/';
	return $path;
}
// 获取客户端IP地址
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL) return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos =  array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip   =  trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

?>