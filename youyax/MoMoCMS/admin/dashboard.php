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
<?php
$sql="select * from ".DB_PREFIX."leave";
$query=$db->query($sql);
$num_all=$query->rowCount();

$sql="select * from ".DB_PREFIX."leave where status=0";
$query=$db->query($sql);
$num_pending=$query->rowCount();

$sql="select * from ".DB_PREFIX."leave where status=1";
$query=$db->query($sql);
$num_pass=$query->rowCount();

$sql="select * from ".DB_PREFIX."leave where admin!='' and con2!='' and time2!=''";
$query=$db->query($sql);
$num_reply=$query->rowCount();

?>
<script type="text/javascript">
$(document).ready(function(){	
	/* setup navigation, content boxes, etc... */
	Administry.setup();	
	/* progress bar animations - setting initial values */
	Administry.progress("#progress1", <?php echo $num_pending; ?>, <?php echo $num_all; ?>);
	Administry.progress("#progress2", <?php echo $num_pass; ?>, <?php echo $num_all; ?>);
	Administry.progress("#progress3", <?php echo $num_reply; ?>, <?php echo $num_all; ?>);
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
			echo out_menu(1,$arr);
			?>
			<!-- End of Main navigation -->
		</div>
	</header>
	<!-- End of Header -->
	<!-- Page title -->
	<div id="pagetitle">
		<div class="wrapper">
			<h1>控制面板</h1>
		</div>
	</div>
	<!-- End of Page title -->
	
	<!-- Page content -->
	<div id="page">
		<!-- Wrapper -->
		<div class="wrapper">
				<!-- Left column/section -->
				<section class="column width6 first">
				
					<div class="colgroup leading">
						<div class="column width3 first">
							<h3>欢迎回来, <?php echo $_SESSION['momocms_admin']; ?></h3>
							<p>
								欢迎再次使用MoMoCMS的后台控制面板
							</p>
						</div>
						<div class="column width3">
							<h4>上次登录</h4>
							<p>
							<?php
								$sql="select * from ".DB_PREFIX."access_log order by id desc limit 0,2";
								$query=$db->query($sql);
								$array=array();
								while($arr=$query->fetch()){
										$array[]=$arr;
								}
								if(!empty($array[1])){
									echo date('l, d, M, Y ,A h:i',$array[1]['time'])." 来自 ".$array[1]['name'].' '.$array[1]['ip'];	
								}else{
									echo '无信息';	
								}
							?>
							</p>
						</div>
					</div>
					
					<div class="colgroup leading">
						<div class="column width3 first">
							<h4>系统信息</h4>
							<hr/>
							<table class="no-style full">
								<tbody>
									<tr>
										<td>操作系统</td>
										<td class="ta-right"><?php echo PHP_OS; ?></td>
									</tr>
									<tr>
										<td>运行环境</td>
										<td class="ta-right"><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
									</tr>
									<tr>
										<td>PHP版本</td>
										<td class="ta-right"><?php echo phpversion(); ?></td>
									</tr>
									<tr>
										<td>数据库版本</td>
										<td class="ta-right"><?php echo $db->getAttribute(PDO::ATTR_SERVER_VERSION); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="column width3">
							<h4>留言状态</h4>
							<hr/>
							<table class="no-style full">
								<tbody>
									<tr>
										<td width="40">待审核</td>
										<td width="50" class="ta-center"><?php echo $num_pending."/".$num_all; ?></td>
										<td align="left"><div id="progress1" class="progress full progress-red"><span><b></b></span></div></td>
									</tr>
									<tr>
										<td width="40">已通过</td>
										<td width="50" class="ta-center"><?php echo $num_pass."/".$num_all; ?></td>
										<td align="left"><div id="progress2" class="progress full progress-green"><span><b></b></span></div></td>
									</tr>
									<tr>
										<td width="40">已回复</td>
										<td width="50" class="ta-center"><?php echo $num_reply."/".$num_all; ?></td>
										<td align="left"><div id="progress3" class="progress full progress-blue"><span><b></b></span></div></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				
					<div class="clear">&nbsp;</div>
				
				</section>
				<!-- End of Left column/section -->
<?php require("./public_side_foot.php"); ?>