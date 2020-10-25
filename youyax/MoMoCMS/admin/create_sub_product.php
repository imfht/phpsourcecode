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
			<?php
			require("./public_menu.php");
			echo out_menu(3,$arr);
			?>
			<!-- End of Main navigation -->
		</div>
	</header>
	<!-- End of Header -->
	<!-- Page title -->
	<div id="pagetitle">
		<div class="wrapper">
			<h1>
				<?php
				$sql="select * from ".DB_PREFIX."product where id=".intval($_GET['id']);
				$query=$db->query($sql);
				$arr=$query->fetch();
				echo '< '.$arr['name'].' > 详细列表';
				?>
				</h1>
		</div>
	</div>
	<!-- End of Page title -->
	
	<!-- Page content -->
	<div id="page">
		<!-- Wrapper -->
		<div class="wrapper">
				<!-- Left column/section -->
				<section class="column width6 first">					
					<div id="successMsg" style="display:none" class="box box-success">产品创建成功</div>
					<h3>创建一个新产品</h3>
					
					<form id="sampleform" enctype="multipart/form-data" method="post" action="./create_sub_product_do.php" target="hiddenframe" onsubmit="document.documentElement.scrollTop = document.body.scrollTop =0;">
					<iframe id="hiddenframe" name="hiddenframe" style="width:0;height:0;"></iframe>
						<fieldset>
							<legend>产品信息</legend>
							<input type="hidden" name="category" value="<?php echo $_GET['id']; ?>">
							<p>
								<label class="required" for="producttitle">产品名称</label><br/>
								<input type="text" id="producttitle" class="half title required" value="" name="producttitle"/>
								<small>e.g. xx手机</small>
							</p>
							<p>
								<label class="required" for="producttitle">展示图片(建议尺寸160像素 * 130像素)</label><br/>
								<input type="file" name="pic" class="required">
							</p>
							<p>
								<label for="productdesc">产品描述</label><br/>
								<textarea id="productdesc" class="large full" name="productdesc" style="height:300px;width:730px;"></textarea>
								<script>
									var editor = new UE.ui.Editor();
    									editor.render("productdesc");
								</script>
							</p>
							<p>
								<label  for="producttitle">排列序号</label><br/>
								<input type="text" name="sort" value="0">
							</p>
							<p class="box"><input type="submit" class="btn btn-green big" value="保存"/></p>

						</fieldset>

					</form>

				</section>
				<!-- End of Left column/section -->
<?php require("./public_side_foot.php"); ?>