<?php
@session_start();
@error_reporting(E_ALL ^ E_NOTICE);
$dirName=dirname(__FILE__);
$docConfig=$dirName.'/../config/doc-config-cn.php';
if(!is_file($docConfig)||filesize($docConfig)==0||filesize($docConfig)==3){require_once($dirName.'/inc/nosetup/setup.html');exit;}else{require_once($docConfig);}
require(ABSPATH.'/inc/function.php');
require_once(ABSPATH.'/inc/class.docencryption.php');
if(is_file(ABSPATH.'/inc/common.php'))require_once(ABSPATH.'/inc/common.php');
require(ABSPATH.'/inc/class.database.php');

$_REQUEST = cleanArrayForMysql($_REQUEST);
$_GET = cleanArrayForMysql($_GET);
$_POST = cleanArrayForMysql($_POST);

function checkPwd($username,$pwd,$flag=false)
{
	global $db;
	$username=get_str($username);
	
	if(!checkSqlStr($username))
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE username='$username' LIMIT 1";
	else
	echo '非法字符';
	
	$rst=$db->get_row($sql);
	if($rst)
	{
		$docEncryption = new docEncryption($pwd);
		if ($rst->pwd==$docEncryption->to_string()) {
			$_SESSION[TB_PREFIX.'admin_email']=$rst->email;
			$_SESSION[TB_PREFIX.'admin_name']=$rst->username;
			$_SESSION[TB_PREFIX.'admin_nickname'] = $rst->nickname;
			$_SESSION[TB_PREFIX.'admin_roleId'] = $rst->role;
			$_SESSION[TB_PREFIX.'admin_userID'] = $rst->id;
			$_SESSION[TB_PREFIX.'admin_right']  = $rst->right;

			if($flag)
			{
				$cookieTime =86400;
				
				setcookie(TB_PREFIX.'username',$rst->username,time()+$cookieTime);
				setcookie(TB_PREFIX.'pwd',$rst->pwd,time()+$cookieTime);
			}

			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
/**
 * 对验证码进行验证
 */
function checkCode($checkcode)
{
	$verifycode=$_SESSION['verifycode'];
	if ($verifycode != $checkcode)
	{
		return false;
	}
	else
	{
		return true;
	}
}

if($_REQUEST['emsg']==1)
{
	$errorMsg='';
}
if($_REQUEST['emsg']==2)
{
	$errorMsg='您的验证码有误,请再试一次.';
}
if($_GET['act']=='login')
{
	if(!isset($_SESSION['loginC']))
	{
	    $_SESSION['loginC']=1;
	}
	else
	{
		if(++$_SESSION['loginC']>=4){
			if(!checkCode($_GET['checkcode']))
			{
				echo '["您的验证码填写错误，请再试一次.",'.$_SESSION['loginC'].']';
				exit;
			}
		}
	}
	if(checkPwd($_GET['username'],$_GET['pwd'],$_GET['remamber']))
	{		
		echo '["yes",'.$_SESSION['loginC'].']';
		exit;		
	}
	else
	{
		echo '["您的用户名与密码不符,请再试一次.",'.$_SESSION['loginC'].',1]';
		exit;
	}
}
if($_GET['act']=='logout')
{
	@session_start();
	@session_destroy();
	setcookie('username','');
	setcookie('pwd','');
	setcookie(session_name(),'',time()-3600);
    $_SESSION = array();
	redirect('login.php');	
}
if($_GET['act']=='showphoto')
{
	$username = $_GET['username'];
	$username=get_str($username);
	if(!checkSqlStr($username))
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE username='$username' LIMIT 1";
	$rst=$db->get_row($sql);
	$photo = is_file(ABSPATH.$rst->cropPic)?$rst->cropPic:$rst->smallPic;
	echo ispic($photo,'/admini/images/avatar.jpg');
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>您正在登录稻壳网站后台管理系统</title>
<style>
html,body{ height:100%; overflow:hidden;}
body{font-size:12px;width:100%;background:#000 url(images/loginbg.jpg) no-repeat center center;}
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td{padding:0;margin:0;} 
h1,h2,h3,h4,h5,h6{font-weight:normal;font-size:100%;} 
ol,ul{list-style:none;}
a{text-decoration:none;blr:expression(this.onFocus=this.blur());} 
a:focus{outline:none;} 
img{border:none;}

.con{ width:100%; height:100%; position:relative;}
.way{ position:absolute; left:50%; top:50%;}
.box{ width:416px; height:436px; background:url(images/box.png); position:relative; left:-208px; top:-258px;}
.boxin{ width:400px; height:420px; padding:8px;}
.box h2{ font-size:30px; font-family:"微软雅黑"; width:364px; height:84px; padding:26px 0 0 36px; color:#ccc;}
.error{ width:400px; height:14px; line-height:14px; text-align:center; color:#FF3300; position:absolute; left:8px; top:140px;}
.username,.password{ width:155px; padding:0 10px 0 36px; height:32px; border:none; line-height:32px; color:#fff; float:left; background:url(images/lgbg.png) no-repeat;}
.password{ background:url(images/lgbg.png) left -32px;}
.lgbtn{ width:108px; height:44px; cursor:pointer; border:none; background:url(images/lgbg.png) left -64px;}
.l1,.l2,.l3,.l4{ width:201px; height:32px; position:absolute; left:106px;}
.l1{ top:182px;}
.l2{ top:251px;}
.l3{ left:153px; top:320px;}

.l4{ top:300px; display:none;}
.l4 img{ float:left; margin-top:6px;}
.code{ width:101px; height:32px; border:none; line-height:32px; text-indent:36px; color:#fff; float:left; margin-right:16px; background:url(images/lgbg.png) left -108px;}

.jia{ width:400px; position:relative; display:none;}
.zhuan{ width:400px; height:130px; background:url(images/zhun.gif) no-repeat center center; display:none;}
.avat{ display:none; width:400px; height:180px;}
.avat #photo{ width:240px; height:104px; padding:35px 0 0 139px;}
.avat #photo img{ width:88px; height:88px; cursor:pointer; padding:16px; background:url(images/avatarbg.png) no-repeat;}
.avat h3{ width:400px; text-align:center; font-size:14px; font-family:"微软雅黑"; color:#ccc;}
.bottom{ position:absolute; width:100%; height:110px; bottom:0; background:url(images/foot.png) no-repeat center top;}
</style>
<script type="text/javascript" src="../inc/js/jquery-1.6.2.min.js"></script>
<!--[if IE 6]><script type="text/javascript" src="../inc/js/EvPng.js"></script>
<script type="text/javascript">
EvPNG.fix('div, ul, img, li, input');
</script>
<script type=text/javascript> EvPNG.fix('#nav li a:hover,#nav li a#currend');</script>
</script>
<![endif]-->

<script>
$(document).ready(function(){
	$(window).keypress(function(e){
	  switch(e.keyCode) {
		case 13:$(".lgbtn").click();;break;
	  }
	});
	$("#form1").submit(function () {
  		if($("#username").val()=='')
		{
			$(".error").text("请输入用户名！");
			return false;
		}
	});
	$(".username").focusout(function() {
		if($(this).val()==""){
			$(".error").text("请输入用户名！");
		}
		else{
			$.ajax({
			type:"GET",
			url:"login.php?act=showphoto",
			data:"username="+$(this).attr('value'),
			timeout:"10000",
			cache:false,                                
			success: function(html){
				$("#photo").html("<img src='"+html+"'>");
				$("#name").html($("#username").val());
				$(".error").text("").animate({top:'280px'},10);
				$(".l1").fadeOut(400);
				setTimeout(function(){
					$(".avat").fadeIn(400);				
					$(".l2").animate({top:'300px'},400);
					$(".l3").animate({top:'350px'},400);
					},400);
				},
				error:function(){	
				}
			});	
		}
	});
	$("#photo").click(function(){
		$(".avat").fadeOut(400);
		$(".l1").delay(400).fadeIn(400);
		$(".error").delay(400).animate({top:'140px'},400);
		if($(".l4").css("display")=="block"){
			$(".l4").fadeOut(400);
			$(".l2").delay(300).fadeIn(400);
			$(".l2").animate({top:'251px'},400);
			$(".l3").delay(700).animate({top:'320px'},400);
		}
		else{
			$(".l2").animate({top:'251px'},400);
			$(".l3").animate({top:'320px'},400);
		}
	});
	$("#threew").live('click',function(){
		var ptop = $(".l2").position();
		if(ptop.top=="251"){
			$(".l2").fadeOut(400);
			$(".l4").animate({top:'251px'},400).fadeIn(400);
		}
		if(ptop.top=="300"){
			$(".l2").fadeOut(400);
			$(".l4").delay(300).fadeIn(400);
		}
		$("#threew").attr('id','submit');
	});
	$("#submit").live('click',function(){
		$.ajax({
			type:"GET",
			url:"login.php?act=login",
			data:{username:$(".username").attr('value'),pwd:$(".password").attr('value'),checkcode:$(".code").attr('value')},
			timeout:"10000",
			cache:false,   
			dataType:"json",                             
			success: function(html){
				if(html[0]==='yes'){
					window.location.href='index.php';
				}else{
				$(".error").text(html[0]);
					if(html[1]>=4)	
					{
						$("#submit").attr('id','threew');
					}
					if(html[2]==1)
					{
						$(".l4").fadeOut(400);
						$(".l2").delay(300).fadeIn(400);
					}
				}
			}
			});	
	});
});
</script>

</head>
<body>
<div class="con">
	<div class="way">
		<div class="box pngFix">
			<form id="form1" name="form1" method="post" action="?act=login">
            <div class="boxin">
				<h2>Welcome</h2>
				<div class="error"><?php echo $errorMsg;?></div>
				<div class="l1"><input name="username" type="text" class="username" /></div>
				<div class="zhuan"></div>					
				<div class="avat">
					<div id="photo"></div>
					<h3 id="name"></h3>
				</div>
				<div class="l2"><input name="pwd" type="password" class="password" /></div>
				<div class="l4"><input name="checkcode" type="text" class="code" /><img src="../inc/verifycode.php" style="cursor:pointer;" onclick="this.src='../inc/verifycode.php?s='+Math.ceil(Math.random()*100000);" /></div>
				<div class="l3"><input name="" type="button" class="lgbtn" <?php echo $_SESSION['loginC']>=4?'id="threew"':'id="submit"'?> /></div>
			</div>
			</form>
		</div>
	</div>
</div>
<div class="bottom pngFix">
	<div class="footer"></div>
</div>
</body>
</html>