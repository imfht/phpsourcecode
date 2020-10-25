<?php
require("./database.php");
require("./Fenye.class.php");
if(empty($_SESSION['email']) || empty($_SESSION['password'])){
	echo '<script>window.location.href="./login.php";</script>';exit;
}
if(isset($_POST['sub_lists'])){
	if(!empty($_POST['ck'])){
		for($i=0;$i<count($_POST['ck']);$i++){
			$sql_tmp="select * from `cms_advertiser_pmb` where t_num=".$_POST['ck'][$i];
			$arr_tmp=mysql_fetch_array(mysql_query($sql_tmp));
			$email_tmp=$arr_tmp['email'];
			if($_SESSION['email']==$email_tmp){
				$sql="update `cms_user_pmb` set status='closed' where t_num=".$_POST['ck'][$i];
				mysql_query($sql);
			}else{
				$sql="update `cms_advertiser_pmb` set status='closed' where t_num=".$_POST['ck'][$i];
				mysql_query($sql);
			}
			$sql="update `cms_advertiser_pmb` set status='closed' where r_num=".$_POST['ck'][$i];
			mysql_query($sql);
		}
	}
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
var zt=false;function selectall(){if(zt==true){zt=false}else{zt=true}var a=document.getElementsByTagName("input");for(var i=0;i<a.length;i++){if(a[i].type=="checkbox")a[i].checked=zt}}function check_pass(){bz=false;var a=document.getElementsByTagName("input");for(var i=0;i<a.length;i++){if(a[i].type=="checkbox"&&a[i].checked){bz=true}}if(!bz){alert('至少选择一个选项');return false}else{return true}}$(function(){var url=window.location.href;url=url.split("=");url=parseInt(url[1]);$(".fy"+url).css({background:"#dfdfdf"})})
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
        	<li><a href="./index.php" class="active">控制面板</a></li> <!-- Use the "active" class for the active menu item  -->
        	<li><a href="./message_send.php">发送信息</a></li>
        	<li><a href="./messager.php">联系人列表</a></li>
        	<li class="logout"><a href="./logout.php">注销</a></li>
        </ul>
        <!-- // #end mainNav -->
        
        <div id="containerHolder">  
                <div id="main">
                	<form name="lists" action="" class="jNice" method="post">
                    	<table cellpadding="0" cellspacing="0">
                    		<tr>
                    			<td width="5%"><input type="checkbox" style="position:relative;top:4px;" onclick="selectall()"></td>
                    			<td width="5%"></td>
							<td width="15%">发信人</td>
							<td width="15%">接收人</td>
							<td width="40%">主题</td>
							<td width="20%">最后更新</td>
						</tr>
						<?php
$sql = "select * from (
		   select num,createdDate,createdByUserNum,updatedDate,updatedByUserNum,message_read,r_num,first_name,last_name,organisation,phone,email,message,subject,t_num,status from `cms_advertiser_pmb` where createdByUserNum='".$_SESSION['createdByUserNum']."' and r_num is null and (status is null or status='open')
				union all 
		   select num,createdDate,createdByUserNum,updatedDate,updatedByUserNum,message_read,r_num,first_name,last_name,organisation,phone,email,message,subject,t_num,status from `cms_user_pmb` where createdByUserNum='".$_SESSION['createdByUserNum']."'  and r_num is null and (status is null or status='open'))tmp  order by t_num desc";
$query=mysql_query($sql);
				$num=mysql_num_rows($query);
				if($num>0){
$fenye = new Fenye($num, 10);
$show  = $fenye->show();
$show  = implode("<span style='width:2px;display:inline-block;'></span>", $show);
$sql   = $fenye->listcon($sql);
$query=mysql_query($sql);
				if($num>0){
					$number=0;
					while($arr=@mysql_fetch_array($query)){
						$number++;
						?>
						<tr <?php if($number%2==1)echo 'class="odd"';?>>
						<td><input type="checkbox" name="ck[]"  style="position:relative;top:4px;" value="<?php echo $arr['t_num']; ?>"></td>
						<td><?php if($arr['message_read']=='0'){ ?>
								<img src="./style/img/bullet.png">
							<?php } ?></td>
						<td>
						<?php
										$lasttime='';
										$sql_tmp_1_1="select * from cms_advertiser_pmb where (t_num=".$arr['t_num']." or r_num=".$arr['t_num'].") order by num desc";
											$query_tmp_1_1=mysql_query($sql_tmp_1_1);
												$arr_tmp_1_1=mysql_fetch_array($query_tmp_1_1);
											$lasttime=$arr_tmp_1_1['createdDate']; //get time
											$sql_tmp_1_1="select * from cms_advertiser_pmb where createdByUserNum=".$_SESSION['createdByUserNum']."  and (t_num=".$arr['t_num']." or r_num=".$arr['t_num'].") order by num desc";
											$query_tmp_1_1=mysql_query($sql_tmp_1_1);
											$num_tmp_1_1=mysql_num_rows($query_tmp_1_1);
											if($num_tmp_1_1>=1){
												$sql_tmp_1_1="select * from cms_advertiser_pmb where (t_num=".$arr['t_num']." or r_num=".$arr['t_num'].") order by num desc";
											$query_tmp_1_1=mysql_query($sql_tmp_1_1);
												$arr_tmp_1_1=mysql_fetch_array($query_tmp_1_1);
												if($arr_tmp_1_1['email']==$_SESSION['email']){
													echo '我';
												}else{
													echo $arr['first_name']." ".$arr['last_name'];
												}
											}else{
														$sql_tmp_1_1="select * from cms_user_pmb where  t_num=".$arr['t_num']." order by num desc";
														$query_tmp_1_1=mysql_query($sql_tmp_1_1);
														$num_tmp_1_1=mysql_num_rows($query_tmp_1_1);
														if($num_tmp_1_1>0){
															$arr_tmp_1_1=mysql_fetch_array($query_tmp_1_1);
															if($arr_tmp_1_1['updatedByUserNum']==0){
																echo '我';
															}else{
																echo $arr['first_name']." ".$arr['last_name'];
															}
														}
											}
										?>
									</td><td>
										<?php
										$lasttime='';
										$sql_tmp_1_1="select * from cms_advertiser_pmb where (t_num=".$arr['t_num']." or r_num=".$arr['t_num'].") order by num desc";
											$query_tmp_1_1=mysql_query($sql_tmp_1_1);
												$arr_tmp_1_1=mysql_fetch_array($query_tmp_1_1);
											$lasttime=$arr_tmp_1_1['createdDate']; //get time
											
											$sql_tmp_1_1="select * from cms_advertiser_pmb where createdByUserNum=".$_SESSION['createdByUserNum']."  and (t_num=".$arr['t_num']." or r_num=".$arr['t_num'].") order by num desc";
											$query_tmp_1_1=mysql_query($sql_tmp_1_1);
											$num_tmp_1_1=mysql_num_rows($query_tmp_1_1);
											if($num_tmp_1_1>=1){
												$sql_tmp_1_1="select * from cms_advertiser_pmb where (t_num=".$arr['t_num']." or r_num=".$arr['t_num'].") order by num desc";
											$query_tmp_1_1=mysql_query($sql_tmp_1_1);
												$arr_tmp_1_1=mysql_fetch_array($query_tmp_1_1);
												if($arr_tmp_1_1['email']==$_SESSION['email']){
													echo $arr['first_name']." ".$arr['last_name'];
												}else{
													echo '我';
												}
											}else{
													$sql_tmp_1_1="select * from cms_user_pmb where t_num=".$arr['t_num']." order by num desc";
														$query_tmp_1_1=mysql_query($sql_tmp_1_1);
														$num_tmp_1_1=mysql_num_rows($query_tmp_1_1);
														if($num_tmp_1_1>0){
															$arr_tmp_1_1=mysql_fetch_array($query_tmp_1_1);
															if($arr_tmp_1_1['updatedByUserNum']!=0){
																echo '我';
															}else{
																echo $arr['first_name']." ".$arr['last_name'];
															}
														}
											}
										?>	
						</td>
						<td>
							<a href="./message_view.php?pnum=<?php echo $arr['t_num']; ?>">
								<?php echo $arr['subject']." (ID:".$arr['t_num'].")"; ?>
							</a>
						</td>
						<td ><?php echo date("Y-m-d H:i:s",$lasttime); ?></td>
						</tr>
					<?php
				}
			}
		}else{
$show='';	
}
?>                                                
                        </table>
                        <div style="background:#fdfcf6;width:100%;margin-top:4px;height:32px;">
                        	<input type="hidden" name="sub_lists">
                        	<input type="button" onclick="if(check_pass()){document.forms['lists'].submit()}" value="删除" style="background:url(./style/img/button-submit.gif);width:94px;height:29px;border:none;cursor:pointer;float:left;">
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