<?php
require("./database.php");
if(isset($_POST['sub'])){
	$sql="select * from cms_accounts where status=1 and email='".addslashes($_POST['email'])."' and password='".addslashes(md5($_POST['pass']))."'";
	$query=mysql_query($sql);
	$num=mysql_num_rows($query);
	if($num>0){
		$arr=mysql_fetch_assoc($query);
		foreach($arr as $k=>$v){
			$_SESSION[$k]=$v;	
		}
		$tip='用户登录成功，正在跳转……';
	}else{
		$tip='很遗憾，登录失败，正在跳转……';	
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="3; url=./index.php" />
<title>Publish Message Board -- 发布信息系统</title>
<link rel="icon" href="./favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
<link rel="bookmark" href="./favicon.ico" type="image/x-icon">
<!-- CSS -->
<link href="style/css/transdmin.css" rel="stylesheet" type="text/css" media="screen" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="style/css/ie7.css" /><![endif]-->

<!-- JavaScripts-->
<script type="text/javascript" src="style/js/jquery.js"></script>
<script type="text/javascript" src="style/js/jNice.js"></script>
</head>

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="#"><span>Publish Message Board</span></a></h1>
        
        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <ul id="mainNav">
        	<li><a href="./index.php" class="active">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">
                <div id="main">
                    	<h3><?php echo $tip;?></h3>
                </div>
                <!-- // #main -->               
                <div class="clear"></div>
            <!-- // #container -->
        </div>	
        <!-- // #containerHolder -->
        
        <p id="footer">个人自主开发PMB系统. <a href="http://www.youyax.com">Powered BY YouYaX</a></p>
    </div>
    <!-- // #wrapper -->
</body>
</html>
