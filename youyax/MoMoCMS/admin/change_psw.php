<?php
require("./database.php");
if(empty($_SESSION['momocms_admin'])){
	header("Location:./index.php");	
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>MoMoCMS -- 更好用的企业建站系统</title>
<meta name="description" content="MoMoCMS -- 更好用的企业建站系统" />
<meta name="keywords" content="MoMoCMS" />
<!-- Favicons --> 
<link rel="icon" href="../resource/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="../resource/favicon.ico" type="image/x-icon">
<link rel="bookmark" href="../resource/favicon.ico" type="image/x-icon">
<!-- Main Stylesheet --> 
<link rel="stylesheet" href="css/style.css" type="text/css" />
<!-- jQuery with plugins -->
<script type="text/javascript" SRC="js/jquery-1.4.2.min.js"></script>
<!-- Could be loaded remotely from Google CDN : <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> -->
<script type="text/javascript" SRC="js/jquery.ui.core.min.js"></script>
<script type="text/javascript" SRC="js/jquery.ui.widget.min.js"></script>
<script type="text/javascript" SRC="js/jquery.ui.tabs.min.js"></script>
<!-- jQuery tooltips -->
<script type="text/javascript" SRC="js/jquery.tipTip.min.js"></script>
<!-- Superfish navigation -->
<script type="text/javascript" SRC="js/jquery.superfish.min.js"></script>
<script type="text/javascript" SRC="js/jquery.supersubs.min.js"></script>
<!-- jQuery form validation -->
<script type="text/javascript" SRC="js/jquery.validate_pack.js"></script>
<script type="text/javascript" charset="utf-8" src="./ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="./ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" src="js/html5.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	/* setup navigation, content boxes, etc... */
	Administry.setup();
	$("#sampleform").validate();
});
</script>
</head>
<body>
	<!-- Header -->
	<header id="top">
		<div class="wrapper">
			<!-- Title/Logo - can use text instead of image -->
			<div id="title"><img SRC="../resource/logo.gif" /><!--<span>Administry</span> demo--></div>
			<!-- Top navigation -->
			<div id="topnav">
				管理员 <b><?php echo $_SESSION['momocms_admin']; ?></b>
				<span>|</span> <a href="./logout.php">注销</a><br />
			</div>
			<!-- End of Top navigation -->
			<!-- Main navigation -->
			<nav id="menu">
				<ul class="sf-menu">
					<li class="current"><a HREF="./dashboard.php">控制面板</a></li>
					<li><a HREF="./page.php">页面管理</a></li>	
					<li>
						<a HREF="./product.php">产品管理</a>
						<ul>
							<li>
								<a HREF="./banner.php">广告显示</a>
							</li>
							<li>
								<a href="javascript:;">产品分类</a>
								<ul>
									<?php foreach($arr as $v){	?>
										<li><a href="./detail_product.php?id=<?php echo $v['id']; ?>"><?php echo $v['name']; ?></a></li>
								<?php	}	?>
								</ul>
							</li>
						</ul>
					</li>
					<li><a HREF="./leave.php">留言管理</a></li>
					<li><a HREF="./mix.php">杂项设置</a></li>	
				</ul>
			</nav>
			<!-- End of Main navigation -->
		</div>
	</header>
	<!-- End of Header -->
	<!-- Page title -->
	<div id="pagetitle">
		<div class="wrapper">
			<h1>更新密码</h1>
		</div>
	</div>
	<!-- End of Page title -->
	
	<!-- Page content -->
	<div id="page">
		<!-- Wrapper -->
		<div class="wrapper">
				<!-- Left column/section -->
				<section class="column width6 first">					
					<div id="successMsg" style="display:none" class="box box-success">密码更新成功</div>
					
					<form id="sampleform" enctype="multipart/form-data" method="post" action="./change_psw_do.php" target="hiddenframe" onsubmit="document.documentElement.scrollTop = document.body.scrollTop =0;">
					<iframe id="hiddenframe" name="hiddenframe" style="width:0;height:0;"></iframe>
						<fieldset>
							<legend>更新密码</legend>

							<p>
								<label class="" for="producttitle">原密码</label><br/>
								<input type="text" id="oldpsw" class="half title" value="" name="oldpsw"/>
							</p>
							
							<p>
								<label class="" for="producttitle">新密码</label><br/>
								<input type="text" id="newpsw" class="half title" value="" name="newpsw"/>
							</p>
						
							<p class="box"><input type="submit" class="btn btn-green big" value="更新" /></p>

						</fieldset>

					</form>

				</section>
				<!-- End of Left column/section -->
				
				<!-- Right column/section -->
				<aside class="column width2">
					<div id="rightmenu">
						<header>
							<h3>帐号属性</h3>
						</header>
						<dl class="first">
							<dt><img width="16" height="16" alt="" SRC="img/key.png"></dt>
							<dd><a href="./change_psw.php">管理员 (<?php echo $_SESSION['momocms_admin']; ?>)</a></dd>
							<dd class="last">
								<?php if($_SESSION['momocms_isAdmin']==1){echo '顶级管理账号';}else{echo '演示账号';} ?>
								</dd>
							
							<dt><img width="16" height="16" alt="" SRC="img/help.png"></dt>
							<dd><a href="javascript:;">技术支持</a></dd>
							<dd class="last">YouYaX出品，必属精品</dd>
						</dl>
					</div>
				</aside>
				<!-- End of Right column/section -->
				
		</div>
		<!-- End of Wrapper -->
	</div>
	<!-- End of Page content -->
	
	<!-- Page footer -->
	<footer id="bottom">
		<div class="wrapper">
			<p>Copyright &copy; 2014 <b> | Powered BY YouYaX</b></p>
		</div>
	</footer>
	<!-- End of Page footer -->
	
	<!-- Scroll to top link -->
	<a href="#" id="totop">回到顶部</a>

<!-- Admin template javascript load -->
<script type="text/javascript" SRC="js/administry.js"></script>
 </body>
</html>