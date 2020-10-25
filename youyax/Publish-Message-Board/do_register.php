<?php
require("./database.php");
if(isset($_POST['sub'])){
if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || empty($_POST['pass']) || empty($_POST['first_name']) 
|| empty($_POST['last_name'])  || empty($_POST['telephone_number'])  || empty($_POST['organisation_name'])){
	echo file_get_contents("./exception.html");
	exit;	
}
$codes = '';
    for ($i = 0; $i < 6; $i++) {
        $codes .= mt_rand(0, 9);
    }
 $sql_tmp="select * from `cms_accounts` where email='".addslashes($_POST['email'])."'";
 $query_tmp=mysql_query($sql_tmp);
 $num_tmp=mysql_num_rows($query_tmp);
 if($num_tmp<=0){
	mysql_query("INSERT INTO `cms_accounts` SET             
                      createdDate               = '".time()."',
                      updatedDate              = '".time()."',
                      email						='".addslashes($_POST['email'])."',
                      password						='".addslashes(md5($_POST['pass']))."',
                      first_name					='".addslashes($_POST['first_name'])."',
                      last_name				='".addslashes($_POST['last_name'])."',
                      telephone_number					='".addslashes($_POST['telephone_number'])."',
                      organisation_name				='".addslashes($_POST['organisation_name'])."',
                      codes				='".$codes."',
                      status =1"
		      )
      or die("MySQL Error:<br/>\n". htmlspecialchars(mysql_error()) . "\n");
      $mid=mysql_insert_id();      
      mysql_query("update `cms_accounts` SET createdByUserNum=".$mid.",updatedByUserNum=".$mid." where num=".$mid);
	}else{
		echo file_get_contents("./exception_email_used.html");
		exit;	
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="3; url=./login.php" />
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
    	<h1><a href="./index.php"><span>Publish Message Board</span></a></h1>
        
        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <div style="clear:both;"></div>
        <ul id="mainNav">
        	<li><a href="./index.php">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">
                
                <div id="main">
					<h3>用户注册成功，正在跳转……</h3>
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
