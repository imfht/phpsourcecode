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
				<a style="float:right;margin:10px 0 5px 0;" href="./create_sub_product.php?id=<?php echo $arr['id']; ?>" class="btn"><span class="icon icon-add">&nbsp;</span>新建产品</a>
				<table id="report" class="stylized full" style="">
						<thead>
							<tr>
								<th>说明文字</th>
								<th class="ta-center">展示图片名称</th>
								<th class="ta-right">操作</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="select * from ".DB_PREFIX."product_sub where category=".$arr['id']." order by sort desc";
								$query=$db->query($sql);
								while($arr=$query->fetch()){
							?>
							<tr>
								<td class="title"><?php echo $arr['name']; ?></td>
								<td class="ta-center"><?php echo $arr['pic']; ?></td>
								<td class="ta-right"><a href="./detail_sub_product.php?id=<?php echo $arr['id']; ?>">详细</a>&nbsp;&nbsp;
								<a href="./delete_sub_product.php?id=<?php echo $arr['id']; ?>">删除</a></td>
							</tr>
						<?php	}	?>
						</tbody>
					</table>
				</section>
				<!-- End of Left column/section -->
<?php require("./public_side_foot.php"); ?>