<?php
/**
 * 123PHPSHOP
 * ============================================================================
 * 版权所有 2015 上海序程信息科技有限公司，并保留所有权利。
 * 网站地址: http://www.123PHPSHOP.com；
 * ----------------------------------------------------------------------------
 * 这是一个免费的软件。您可以在商业目的和非商业目的地前提下对程序除本声明之外的
 * 代码进行修改和使用；您可以对程序代码以任何形式任何目的的再发布，但一定请保留
 * 本声明和上海序程信息科技有限公司的联系方式！本软件中使用到的第三方代码版权属
 * 于原公司所有。上海序程信息科技有限公司拥有对本声明和123PHPSHOP软件使用的最终
 * 解释权！
 * ============================================================================
 *  作者:	123PHPSHOP团队
 *  手机:	13391334121
 *  邮箱:	service@123phpshop.com
 */
?>
<?php
include_once($_SERVER['DOCUMENT_ROOT']."/Connections/localhost.php");	 

//	这里需要检查是否已经安装过了，如果已经安装过了，那么直接跳转到首页
$error			=array();
$uploads_folder	=$_SERVER['DOCUMENT_ROOT']."/uploads";
$config_folder	=$_SERVER['DOCUMENT_ROOT']."/Connections";
$index_path		=$_SERVER['DOCUMENT_ROOT']."/index.php";

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['db_host']) && isset($_POST['db_username']) && isset($_POST['db_password']) && isset($_POST['db_name'])){
 	if(trim($hostname_localhost)!=''){
		// _to_index();
		//	return;
	}
	
	// 如果没有安装，那么检查数据库是否可以链接，如果不能链接，那么告知
	if(!_db_could_connect($_POST['db_host'],$_POST['db_username'],$_POST['db_password'],$_POST['db_name'])){
 		$error[]="数据库无法连接，请检查输入的参数";
	}elseif(!_import_sql($_POST['admin_username'],$_POST['admin_password'])){
		// 如果可以链接，那么进行数据的导入，如果导入失败，那么告知
		$error[]="数据库导入错误".mysql_error();
		
	}elseif(!_check_dir_writable($uploads_folder)){
		//	检查上传文件夹是否可写，如果不可写，那么告知
		$error[]="上传文件：$uploads_folder夹不可写，无法完成安装，请联系系统管理员修改这个文件夹的读写属性";
	}elseif(!_check_dir_writable($config_folder)){
		// 检查配置文件是否可写，如果不可写，那么告知
		$error[]="系统配置文件夹:$config_folder不可写，无法完成安装，请联系系统管理员修改这个文件夹的读写属性";
	}elseif(!_write_config()){
		$error[]="配置文件写入错误";
	
	}else{
		// 如果所有的操作都成功，那么直接跳转到首页，
		_to_index();
	}
}

// 导入sql
function _import_sql($admin_username,$admin_password){
		$filename				=$_SERVER['DOCUMENT_ROOT']."/install/123phpshop.sql";
 
	//	这里需要将默认的管理员的密码设置为
	 // sql文件包含的sql语句数组
 	$templine = '';
 	$lines = file($filename);
	mysql_query("set names utf8");
  	foreach ($lines as $line)
	{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		
		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			 mysql_query($templine);
			// Reset temp variable to empty
			$templine = '';
		}
	}
		if(!mysql_query("insert  into `member`(`id`,`username`,`password`,`mobile`,`email`,`register_at`,`mobile_confirmed`,`birth_date`,`is_delete`,`last_login_at`,`last_login_ip`) values (1,'".$_POST['admin_username']."','".md5($_POST['admin_password'])."','13391334121','service@123phpshop.com',NULL,'1',NULL,0,'','127.0.0.1');
	")){
		return false;
	}
        return true;

}
function _check_dir_writable($uploads_folder){
	$uploads_folder		=$_SERVER['DOCUMENT_ROOT']."/uploads";
	if(!is_dir($uploads_folder)){
		return false;
	}
	return is_writable($uploads_folder);
}

function _write_config(){
	$config_folder		=$_SERVER['DOCUMENT_ROOT']."/Connections";
	if(!is_dir($config_folder)){
		return false;
	}
	$config_file=$config_folder."/localhost.php";
	$file_content='
<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
error_reporting(0); 
$hostname_localhost = "'.$_POST['db_host'].'";
$database_localhost = "'.$_POST['db_name'].'";
$username_localhost = "'.$_POST['db_username'].'";
$password_localhost = "'.$_POST['db_password'].'";
$localhost = mysql_pconnect($hostname_localhost, $username_localhost, $password_localhost) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_query("set names utf8");

if (!isset($_SESSION)) {
  session_start();
}
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/check_install.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/const.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/lib/common.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/lib/cart.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/check_admin_login.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/Connections/check_user_login.php";
?>';
	try{
		$f=fopen($config_file,"w");
		fwrite($f,$file_content);
		fclose($f);
		return true;
	}catch(Exception $e){
		return false;
	}	
}

function _db_could_connect($db_host,$db_username,$db_password,$db_name){
	if(!mysql_connect($db_host,$db_username,$db_password)){
		return false;
	}
	return mysql_select_db($db_name);
}

function _to_index(){
	$insertGoTo="/index.php";
	header(sprintf("Location: %s", $insertGoTo));
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>123PHPSHOP-安装</title>
<style>
tr{
	height:36px;
}
</style>
<link href="/css/common_admin.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.STYLE1 {
	color: #FF0000;
	font-size: 16px;
}
-->
</style>

</head>
<body>
<?php if(count($error)>0){ ?>
  <p class="phpshop123_infobox">
  	<?php foreach($error as $error_item){ 
  		echo $error_item;
  	  } ?>
  </p>
<?php } ?>
<form id="install_form" name="install" method="post" action="">
  <h1 align="center" class="phpshop123_title">上海序程信息科技有限公司123PHPSHOP安装程序</h1>
  <table width="957" border="0" cellpadding="0" cellspacing="0" class="phpshop123_form_box">
    <tr>
      <th colspan="2" scope="row" align="center"><div align="center" class="STYLE1">安装完毕之后请务必删除服务器上的install安装文件夹!</div></th>
    </tr>
    <tr>
      <th width="163" scope="row"><div align="right">数据库地址：</div></th>
      <td width="778"><label>
        <input name="db_host"  id="db_host"  type="text" value="localhost" maxlength="32" />
      </label></td>
    </tr>
    <tr>
      <th scope="row"><div align="right">数据库名称：</div></th>
      <td><input name="db_name" id="db_name"  type="text" value="123phpshop" maxlength="32" /></td>
    </tr>
    <tr>
      <th scope="row"><div align="right">数据库账户：</div></th>
      <td><input name="db_username" id="db_username"  type="text" value="root" maxlength="32" /></td>
    </tr>
    <tr>
      <th scope="row"><div align="right">数据库密码：</div></th>
      <td><input name="db_password" id="db_password" type="text" maxlength="32" /></td>
    </tr>
    <tr style="border-top:2px solid #CCCCCC;">
      <th scope="row"><div align="right">后台管理员账户：</div></th>
      <td><label>
        <input name="admin_username" type="text" id="admin_username" value="admin" maxlength="32" />
      </label></td>
    </tr>
    <tr>
	
      <th scope="row"><div align="right">后台管理员密码：</div></th>
      <td><input name="admin_password" type="text" id="admin_password" maxlength="32" /></td>
    </tr>
    <tr>
      <th scope="row"><div align="right">管理员密码确认：</div></th>
      <td><input name="admin_passconf" type="text" id="admin_passconf" maxlength="32" /></td>
    </tr>
    <tr>
      <td scope="row">&nbsp;</td>
      <td><label>
        <input type="submit" name="Submit" value="我接受123PHPSHOP服务协议并安装" />
      </label></td>
    </tr>
  </table>
  <p align="center">上海序程信息科技有限公司，版权所有，违者必究</p>
</form>
<script language="JavaScript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script>
$().ready(function(){

 	$("#install_form").validate({
        rules: {
            db_host: {
                required: true
            },
            db_name: {
                required: true
             },
            db_username: {
                required: true
             },
			 
			 admin_username: {
				required: true
             },
            admin_password: {
				required: true,
                 minlength: 8  
            },
            admin_passconf: {
				required: true,
                minlength: 8 ,
				equalTo:"#admin_password"
            }
        },
        messages: {
            db_host: {
                required: "必填" 
            },
            db_name: {
                required: "必填" 
               },
            db_username: {
                required: "必填"
             },
			 admin_username: {
			 	required: "必填"
             },
            admin_password: {
			 	required: "必填",
                minlength: "至少要8个字符哦" 
            },
            admin_passconf: {
			 required: "必填",
                 minlength:  "至少要8个字符哦",
				 equalTo:"管理员密码不一致" 
            }
        }
    });
	
});</script>
</body>
</html>
