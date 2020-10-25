<?php
require("./database.php");
require("./Fenye.class.php");
if(empty($_SESSION['email']) || empty($_SESSION['password'])){
	echo '<script>window.location.href="./login.php";</script>';exit;
}
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
<style>.page a {
font-weight: normal;
display: inline-block;
padding: 0 8px;
height: 26px;
line-height: 26px;
border: 1px solid #e6e6e6;
background-color: #FFF;
color: #333;
overflow: hidden;
text-decoration: none;
}.page a:hover {
color: #4b9605;
border-color: #b3c874;
text-decoration: none;
}</style>
<script>
//var zt=false;function selectall(){if(zt==true){zt=false}else{zt=true}var a=document.getElementsByTagName("input");for(var i=0;i<a.length;i++){if(a[i].type=="checkbox")a[i].checked=zt}}function check_pass(){bz=false;var a=document.getElementsByTagName("input");for(var i=0;i<a.length;i++){if(a[i].type=="checkbox"&&a[i].checked){bz=true}}if(!bz){alert('至少选择一个选项');return false}else{return true}}
$(function(){var url=window.location.href;url=url.split("=");url=parseInt(url[1]);$(".fy"+url).css({background:"#dfdfdf"})})
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
        	<li><a href="./index.php" >控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        	<li><a href="./message_send.php">发送信息</a></li>
        	<li><a href="./messager.php" class="active">联系人列表</a></li>
        	<li class="logout"><a href="./logout.php">注销</a></li>
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">  
                <div id="main">
                	<form name="lists" action="" class="jNice" method="post">
                    	<table cellpadding="0" cellspacing="0">
                    		<tr>
                    			<!--<td width="10%"><input type="checkbox" style="position:relative;top:4px;" onclick="selectall()"></td>-->
							<td width="30%">联系人</td>
							<td width="30%">公司名称</td>
							<td width="20%">注册时间</td>
							<td width="10%">操作</td>
						</tr>
						<?php
						$sql="select * from cms_accounts where email='xujinliang1227@163.com'";
						$arr=mysql_fetch_array(mysql_query($sql));
						?>
						<!-- 管理员联系方式start -->
						<tr>
							<td style="background:#fdfcf6"><?php echo $arr['last_name'].' '.$arr['first_name'] ?></td>
							<td style="background:#fdfcf6"><?php echo $arr['organisation_name']; ?></td>
							<td style="background:#fdfcf6"><?php echo date('Y-m-d H:i:s',$arr['createdDate']);?></td>
							<td style="background:#fdfcf6"><a href="./message_send.php?num=<?php echo $arr['num']; ?>">发送信息</a></td>
						</tr>
						<!-- 管理员联系方式end -->
					<?php
$sql="select count(*) from cms_accounts where email!='".$_SESSION['email']."' order by num desc";
$query=mysql_query($sql);
$num=mysql_num_rows($query);
if($num>0){
$arr=mysql_fetch_array($query);
$num=$arr[0];
$fenye = new Fenye($num, 10);
$show  = $fenye->show();
$show  = implode("<span style='width:2px;display:inline-block;'></span>", $show);
$sql="select * from cms_accounts  where email!='".$_SESSION['email']."' order by num desc";
$sql   = $fenye->listcon($sql);
$query=mysql_query($sql);
				if($num>0){
					$number=0;
					while($arr=@mysql_fetch_array($query)){
						$number++;
						?>
					<tr <?php if($number%2==1)echo 'class="odd"';?>>
					<!--<td><input type="checkbox" name="ck[]"  style="position:relative;top:4px;" value="<?php echo $arr['createdByUserNum']; ?>"></td>-->
					<td><?php echo $arr['last_name'].' '.$arr['first_name'] ?></td>
					<td><?php echo $arr['organisation_name']; ?></td>
					<td><?php echo date('Y-m-d H:i:s',$arr['createdDate']);?></td>
					<td><a href="./message_send.php?num=<?php echo $arr['num']; ?>">发送信息</a></td>
					</tr>
		<?php	}}} else{$show='';}  ?>
                        </table>
                        <div style="background:#fff;width:100%;margin-top:4px;height:32px;">
                        	<!--<input type="hidden" name="sub_lists">
                        	<input type="button" onclick="if(check_pass()){document.forms['lists'].submit()}" value="删除" style="background:url(./style/img/button-submit.gif);width:94px;height:29px;border:none;cursor:pointer;float:left;">-->
                        	<div class="page" style="float:right;"><?php echo $show; ?></div>
                        </div>
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