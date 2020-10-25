<?php
require("./database.php");
?>
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
<script>
function check(){var error=0;if(document.getElementById("subject").value==''){document.getElementById("subject").style.background='#ffffd5';error=1}else{document.getElementById("subject").style.background='#ffffff'}if(document.getElementById("ta").value==''){document.getElementById("ta").style.background='#ffffd5';error=1}else{document.getElementById("ta").style.background='#ffffff'}if(error==0){return true}else{return false}}
</script>
</head>

<body>
	<div id="wrapper">
    	<!-- h1 tag stays for the logo, you can use the a tag for linking the index page -->
    	<h1><a href="./index.php"><span>Publish Message Board</span></a></h1>
        
        <!-- You can name the links with lowercase, they will be transformed to uppercase by CSS, we prefered to name them with uppercase to have the same effect with disabled stylesheet -->
        <p style="float:right;color:#9b9b9b;"><?php echo $_SESSION['email'];?></p>
        <div style="clear:both;"></div>
        <ul id="mainNav">
        	<li><a href="./index.php">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        	<li><a href="./message_send.php" class="active">发送信息</a></li>
        	<li><a href="./messager.php">联系人列表</a></li>
        	<li class="logout"><a href="./logout.php">注销</a></li>
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">           
                <!-- h2 stays for breadcrumbs -->                
                <div id="main">
                	<form enctype="multipart/form-data" action="./message_send_successful.php" class="jNice" method="post" onsubmit="return check()">
					<h3>发送信息</h3>
                    	<fieldset>
                        	<input type="hidden" name="num" value="<?php echo intval(@$_GET['num']); ?>">
                        	<p><label>主题:</label><input type="text" class="text-long" name="subject" id="subject"/></p>
                        	<p><label>内容:</label><textarea rows="1" cols="1" name="ta" id="ta"></textarea></p>
                        	<p><label>附件</label><input type="file" name="attachfile"></p>
                           <input name="sub" type="submit" value="发送消息" />
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
