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
			echo out_menu(4,$arr);
			?>
			<!-- End of Main navigation -->
		</div>
	</header>
	<!-- End of Header -->
	<!-- Page title -->
	<div id="pagetitle">
		<div class="wrapper">
			<h1>留言管理</h1>
		</div>
	</div>
	<!-- End of Page title -->
	
	<!-- Page content -->
	<div id="page">
		<!-- Wrapper -->
		<div class="wrapper">
				<!-- Left column/section -->
				<section class="column width6 first">
				<table id="report" class="stylized full" style="">
						<thead>
							<tr>
								<th width="270">留言内容</th>
								<th width="50" class="ta-center">留言人</th>
								<th class="ta-center">留言时间</th>
								<th class="ta-center">通过?</th>
								<th class="ta-center">回复?</th>
								<th class="ta-right">操作</th>
							</tr>
						</thead>
						<tbody>
							<?php							
							$sql="select * from ".DB_PREFIX."leave order by status asc,id desc";
							$query=$db->query($sql);
							$num_all=$query->rowCount();
							$page_size=5;
							$page_count=ceil($num_all/$page_size);
							$offset=$page_size*intval(!empty($_GET['page'])?($_GET['page']-1):0);
							$sql="select * from ".DB_PREFIX."leave order by status asc,id desc limit ".$offset." , ".$page_size;
							$query=$db->query($sql);
							$num=$query->rowCount();
							if($num>0){
								while($arr=$query->fetch()){
							?>
							<tr>
								<td class="title">
									<span style="width:270px;display:inline-block;word-break:break-all;">
										<?php echo $arr['con1']; ?>
									</span>
								</td>
								<td class="ta-center"><?php echo $arr['user']; ?></td>
								<td class="ta-center"><?php echo date('Y-m-d H:i:s',$arr['time1']); ?></td>
								<td class="ta-center"><?php echo $arr['status']==1?'<span style="color:green">√</span>':'<span style="color:red">×</span>'; ?></td>
								<td class="ta-center"><?php echo !empty($arr['con2'])?'<span style="color:green">√</span>':'<span style="color:red">×</span>'; ?></td>
								<td class="ta-right">
									<a href="./leave_pass.php?id=<?php echo $arr['id']; ?>">通过</a>&nbsp;&nbsp;
									<a href="./leave_reply.php?id=<?php echo $arr['id']; ?>">回复</a>&nbsp;&nbsp;
									<a href="./leave_delete.php?id=<?php echo $arr['id']; ?>">删除</a></td>
							</tr>
						<?php	}}	?>
						</tbody>
					</table>
<div style="text-align:center"><a href="<?php
	if(intval($_GET['page'])>1){
	parse_str($_SERVER['QUERY_STRING'],$myArray);
			$myArray['page']=$_GET['page']-1;
		echo "./leave.php?".http_build_query($myArray);}?>
	">上一页</a>&nbsp;<a href="<?php
		if((intval($_GET['page'])<$page_count) && ($num_all>$page_size)){
		parse_str($_SERVER['QUERY_STRING'],$myArray);
			$myArray['page']=(empty($_GET['page'])?1:$_GET['page'])+1;
		echo "./leave.php?".http_build_query($myArray);}?>
		">下一页</a>&nbsp;,&nbsp;<?php if(empty($page_count)){echo '0';}else{echo empty($_GET['page'])?1:intval($_GET['page']);} ?> / <?php echo $page_count; ?></div>
				</section>
				<!-- End of Left column/section -->
<?php require("./public_side_foot.php"); ?>