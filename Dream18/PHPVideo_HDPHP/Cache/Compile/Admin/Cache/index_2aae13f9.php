<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>更新缓存</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
		<div class="bg border padding margin-little-bottom">首次安装本视频管理系统必须更新全站缓存！</div>
		<form action="<?php echo U('index');?>" method="POST">
			<table class="table table-bordered table-hover table-condensed table-responsive">
				<tr>
					<td align="right" width="200" colpan="4">选择更新</td>
					<td>
						<table class="table table-bordered table-hover table-condensed table-responsive">
							<tr>
								<td><label><input type="checkbox" name="Action[]" value="Config" checked=''/>配置项缓存</label></td>
							</tr>
							<tr>
								<td><label><input type="checkbox" name="Action[]" value="Cate" checked=''/>频道缓存</label></td>
							</tr>
							<tr>
								<td><label><input type="checkbox" name="Action[]" value="Addons" checked=''/>插件缓存</label></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<button class="button bg">
							<i class="icon-edit"></i>
							开始更新
						</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>