<?php
session_start();
if ($_SESSION['login_type'] == 1) {
} else {
	$_SESSION['nolog'] = "请先登录";
	header("Location:../index.php");
}
?>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>ChsTube-下载</title>
		<script type="text/javascript" src="../js/jquery.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.js" ></script>
		<link rel="stylesheet" href="../css/bootstrap.css" />
	</head>
	<body>
		<div class="container">
			<div class="row">
				</br>
				</br>
				</br>
				</br>
				<div class="well col-lg-12" align="center">
					<h1>下载页面</h1>
					</br>
					<p>
						<div class="panel panel-success">
							<div class="panel-heading">
								此视频的下载选项
							</div>
							<div class="panel-body">
								<p>
									以下为当前该视频已经转换完毕的格式 如果您需要其他格式 请点击下方选择其他格式进行选择
								</p>
							</div>
							<div></div>
							<table class="table">
								<tr>
									<th width="80%">描述</th>
									<th>下载地址</th>
								</tr>
								<tr>
									<td>1080p</td>
									<td>
										<a href="#" class="btn btn-primary">
											<i class="glyphicon glyphicon-download-alt"></i>&nbsp;点击下载
										</a></td>
								</tr>
							</table>
							<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
							<i class="glyphicon glyphicon-th-list"></i>&nbsp;其他格式
							</button>
							&nbsp;
							<a class="btn btn-default btn-lg" href="../index.php">
								<i class="glyphicon glyphicon-circle-arrow-left"></i>&nbsp;返回主页
							</a>
						</div>
					</p>
				</div>
			</div>
		</div>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
						</button>
						<h4 class="modal-title" id="myModalLabel"></h4>
					</div>
					<div class="modal-body">
						<div class="alert alert-danger">
							<strong>警告:</strong>你确定要把你的任务提交到解析服务器吗？
						</div>
						<div class="alert alert-info">
							<strong>提示:</strong>提交后请到个人中心查看任务并选择清晰度!
						</div>
					</div>
					<div class="modal-footer">
						<form action="putup.php?re=1" method="post">
							<button type="submit" type="button" class="btn btn-primary">
							确认提交
							</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">
							取消
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>