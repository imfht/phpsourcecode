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
<script type="text/javascript" src="js/html5.js"></script>
<script type="text/javascript">
$(document).ready(function(){	
	/* setup navigation, content boxes, etc... */
	Administry.setup();
	Administry.expandableRows();
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
			<h1>产品管理</h1>
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
				<form name="form" enctype="multipart/form-data" method="post" action="./create_product.php" target="hiddenframe">
				<iframe id="hiddenframe" name="hiddenframe" style="width:0;height:0;"></iframe>
				<div style="float:right;margin-top:10px;">
					<input type="text" required="required" name="productname">
					<a href="javascript:;" class="btn" style="position:relative;top:2px;top:0px\9;" onclick="if(document.forms['form'].productname.value!=''){document.forms['form'].submit()}"><span class="icon icon-add">&nbsp;</span>增加产品</a>
				</div>
				</form>
				<table id="report" class="stylized full" style="">
						<thead>
							<tr>
								<th>产品分类</th>
								<th class="ta-right">操作</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($arr as $v){
							?>
							<tr>
								<td class="title">
									<div>
										<a href="#"><b><?php echo $v['name']; ?></b></a>
										<div class="listingDetails">
											<div class="pad">
												<b>更新产品分类名称</b>
												<form name="form_product<?php echo $v['id']; ?>" action="./update_product.php" method="post" target="hiddenframe">
													<input type="text" value="<?php echo $v['name']; ?>" name="product_newname" placeholder="输入新的产品名称">
													<input type="text" value="<?php echo $v['sort']; ?>" name="product_sort" placeholder="排列序号">
													<input type="hidden" name="product_id" value="<?php echo $v['id']; ?>">
													<a href="javascript:;" class="btn btn-green" style="position:relative;top:2px;top:0px\9;" onclick="document.forms['form_product<?php echo $v['id']; ?>'].submit()">更新</a>
												</form>
											</div>
										</div>
									</div>
								</td>
								<td class="ta-right">
									<a href="./detail_product.php?id=<?php echo $v['id']; ?>">详细</a>&nbsp;&nbsp;
									<a href="./delete_product.php?id=<?php echo $v['id']; ?>">删除</a></td>
							</tr>
						<?php	}	?>
						</tbody>
					</table>
				</section>
				<!-- End of Left column/section -->
<?php require("./public_side_foot.php"); ?>