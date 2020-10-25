<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>MoMoCMS安装界面</title>
<meta name="description" content="MoMoCMS -- 更好用的企业建站系统" />
<meta name="keywords" content="MoMoCMS" />
<meta name="renderer" content="webkit">
<!-- Favicons --> 
<link rel="icon" href="../resource/favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="../resource/favicon.ico" type="image/x-icon">
<link rel="bookmark" href="../resource/favicon.ico" type="image/x-icon">
<!-- Main Stylesheet --> 
<link rel="stylesheet" href="../admin/css/style.css" type="text/css" />
<!-- Your Custom Stylesheet --> 
<link rel="stylesheet" href="../admin/css/custom.css" type="text/css" />
<!-- jQuery with plugins -->
<script type="text/javascript" SRC="../admin/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" SRC="../admin/js/jquery.ui.core.min.js"></script>
<script type="text/javascript" SRC="../admin/js/jquery.ui.widget.min.js"></script>
<script type="text/javascript" SRC="../admin/js/jquery.ui.tabs.min.js"></script>
<!-- Superfish navigation -->
<script type="text/javascript" SRC="../admin/js/jquery.superfish.min.js"></script>
<script type="text/javascript" SRC="../admin/js/jquery.supersubs.min.js"></script>
<!-- jQuery form validation -->
<script type="text/javascript" SRC="../admin/js/jquery.validate_pack.js"></script>
<!-- jQuery popup box -->
<script type="text/javascript">

$(document).ready(function(){
	
	/* setup navigation, content boxes, etc... */
	
	// validate signup form on keyup and submit
	var validator = $("#loginform").validate({
		rules: {
			db_host: "required",
			db_name: "required",
			db_user: "required",
			db_prefix: "required",
			admin: "required",
			password: "required",
			url: "required"
		},
		messages: {
			db_host: "数据库地址必填",
			db_name: "数据库名必填",
			db_user: "数据库用户名必填",
			db_prefix: "数据表前缀必填",
			admin: "管理员账号必填",
			password: "管理员密码必填",
			url: "网站首页地址必填"
		},
		// the errorPlacement has to take the layout into account
		errorPlacement: function(error, element) {
			error.insertAfter(element.parent().find('label:first'));
		}
	});
});
</script>
</head>
<body>
	<!-- Header -->
	<header id="top">
		<div class="wrapper-login">
			<!-- Title/Logo - can use text instead of image -->
			<div id="title"><img SRC="../resource/logo.gif" /><!--<span>Administry</span> demo--></div>
			<!-- Main navigation -->
			<nav id="menu">
				<ul class="sf-menu">
					<li class="current"><a href="javascript:;">安装界面</a></li>
				</ul>
			</nav>
		</div>
	</header>
	<!-- End of Header -->
	<!-- Page title -->
	<div id="pagetitle">
		<div class="wrapper-login"></div>
	</div>
	<!-- End of Page title -->
	
	<!-- Page content -->
	<div id="page">
		<!-- Wrapper -->
		<div class="wrapper-login">
				<!-- Login form -->
				<section class="full">					
					<h3></h3>
					<div class="box box-info">欢迎使用MoMoCMS, 请完成下面的安装<br>为更好的体验，默认带上安装数据包</div>
					<form id="loginform" enctype="multipart/form-data" method="post" action="./install_do.php" target="hiddenframe" onsubmit="document.documentElement.scrollTop = document.body.scrollTop =0;">
					<iframe id="hiddenframe" name="hiddenframe" style="width:0;height:0;"></iframe>
						<p>
							<label class="required">数据库地址:</label><br/>
							<input type="text" id="db_host" class="full" value="localhost" name="db_host"/>
						</p>						
						<p>
							<label class="required">数据库名:</label><br/>
							<input type="text" id="db_name" class="full" value="momocms" name="db_name"/>
						</p>
						<p>
							<label class="required">数据库用户名:</label><br/>
							<input type="text" id="db_user" class="full" value="root" name="db_user"/>
						</p>
						<p>
							<label>数据库密码:</label><br/>
							<input type="password" id="db_psw" class="full" value="root" name="db_psw"/>
						</p>
						<p>
							<label class="required">数据表前缀:</label><br/>
							<input type="text" id="db_prefix" class="full" value="momo_" name="db_prefix"/>
						</p>
						<p>
							<label class="required">管理员帐号:</label><br/>
							<input type="text" id="admin" class="full" value="" name="admin"/>
						</p>
						<p>
							<label class="required">管理员密码:</label><br/>
							<input type="password" id="password" class="full" value="" name="password"/>
						</p>
						<!--<p>
							<label class="required">管理员邮箱:</label><br/>
							<input type="text" id="email" class="full" value="" name="email"/>
						</p>-->
						<p>
							<label class="required">网站首页地址(带上http://):</label><br/>
							<input type="text" id="url" class="full" value="" name="url"/>
						</p>

						<p>
							<input type="submit" class="btn btn-green big" value="创建安装"/> 
						</p>
						<div class="clear">&nbsp;</div>

					</form>			
				</section>
				<!-- End of login form -->
				
		</div>
		<!-- End of Wrapper -->
	</div>
	<!-- End of Page content -->
	
	<!-- Page footer -->
	<footer id="bottom">
		<div class="wrapper-login">
			<p>Copyright &copy; 2014 <b> | Powered BY YouYaX</b></p>
		</div>
	</footer>
	<!-- End of Page footer -->
	</body>
</html>