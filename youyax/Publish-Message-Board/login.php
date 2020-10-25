<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
        <ul id="mainNav">
        	<li><a href="./index.php" class="active">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">
                <div id="main" style="margin:20px auto 0 auto;width:700px;padding:0;float:none;">
                	<form action="./dologin.php" class="jNice" method="post">
                    	<fieldset>
                    		<p><label>邮箱名</label></p>
                    		<p><input type="text" name="email" class="text-long"></p>
                    		<p><label>密码</label></p>
                    		<p><input type="password" name="pass" class="text-long"></p>
                    		<input name="sub" type="submit" value="用户登录" />
                    		<label><a style="position:relative;left:10px;top:10px;" href="./register.php">用户注册</a></label>
                    	</fieldset>
                    </form>
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
