<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
session_start();
error_reporting(E_ALL ^ E_NOTICE);
@include __Root__.'/Public/Resources/Config.php';
@include __Root__.'/Index/Point/Index_Config_Action.php';
$url=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);
$link = $Mark_Config_Action['root_link'];
$level = $Mark_Config_Action['level'];
$root_file = $Mark_Config_Action['root_file'];
$_SESSION['level'] = $Mark_Config_Action['level'];
if (isset($_SESSION['Mark_Login'])) {
	header("Location:".$url."/".$link."/index.php"); //重新定向到其他页面
    	exit();
}
if (isset($_POST['Post_Action'])) {
$administrator = $Mark_Config_Action['user_name'];
$password = $Mark_Config_Action['user_pass'];
if (isset($_SESSION['Mark_Login'])) { //判断SESSINON中是否有登陆
	 header("Location:".$level."/".$link."/index.php"); //重新定向到其他页面
    exit;
}
$root = $_POST['username'];
$pass = MD5($_POST['password']);
if ($root== '') { //判断POST来的用户名是否为空
    echo "<script language=javascript>alert('Please,Check Your Password!');window.location='".$level."/".$root_file."'</script>";
} elseif ($pass == '') { //判断POST来的密码名是否为空
   	echo "<script language=javascript>alert('Please,Check Your Password!');window.location='".$level."/".$root_file."'</script>";
} else { //两者不为空是判断用户名与密码是否正确
    if ($root == $administrator && $password == $pass) {
        $_SESSION["Mark_Login"] = "Mark_Login"; //注册新的变量,保存当前会话的昵称
         header("Location:".$level."/".$link."/index.php");  //登录成功重定向到管理页面
        
    } else {
   echo "<script language=javascript>alert('Please,Check Your Password!');window.location='".$level."/".$root_file."'</script>";
    }
}
}
?> 
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $Mark_Config_Action['site_name']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?php __ROOT__('Css/templatemo_style.css'); ?>" rel="stylesheet" type="text/css">	
</head>
<body class="templatemo-bg-image-1">
	<div class="container">
		<div class="col-md-12">			
			<form class="form-horizontal templatemo-login-form-2" role="form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
			<input type="hidden" name="Post_Action" value=""/>
				<div class="row">
					<div class="col-md-12">
						<h1><?php echo $Mark_Config_Action['site_name']; ?></h1>
					</div>
				</div>
				<div class="row">
					<div class="templatemo-one-signin col-md-6">
				        <div class="form-group">
				          <div class="col-md-12">		          	
				            <label for="username" class="control-label">Username</label>
				            <div class="templatemo-input-icon-container">
				            	<i class="fa fa-user"></i>
				            	<input type="text" class="form-control" id="username" name="username" placeholder="">
				            </div>		            		            		            
				          </div>              
				        </div>
				        <div class="form-group">
				          <div class="col-md-12">
				            <label for="password" class="control-label">Password</label>
				            <div class="templatemo-input-icon-container">
				            	<i class="fa fa-lock"></i>
				            	<input type="password" class="form-control" id="password" name="password" placeholder="">
				            </div>
				          </div>
				        </div>
				         <div class="form-group">
				          <div class="col-md-12">
				            <input type="submit" value="LOG IN" class="btn btn-warning">
				          </div>
				        </div>
				       </div>
					<div class="templatemo-other-signin col-md-6">
						<label class="margin-bottom-15">
							<a href="<?php echo $url; ?>">←前台</a>
						</label>
						<a class="btn btn-block btn-social btn-facebook margin-bottom-15">
						   © CopyRight 2014<?php Year(); ?>
						</a>
						<a class="btn btn-block btn-social btn-twitter margin-bottom-15">
						    <a href="http://www.creativecommons.org/licenses/by-nc-nd/3.0/cn/legalcode" target="_blank">署名-非商业性使用-禁止演绎 3.0</a>
						</a>
						</div>   
				</div>				 	
		      </form>		      		      
		</div>
	</div>
</body>
</html>