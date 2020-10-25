<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>添加配置组</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<form action="" method="POST">
	<table class="table table-bordered table-hover table-condensed table-responsive">
		<tr>
			<td colspan="3" class="bg text-center padding-little-top padding-little-bottom">配置组添加</td>
		</tr>
		<tr>
			<td align="right">组名称(中文)</td>
			<td><input type="text" name="ctitle" /></td>
			<td>请输入配置组中文名称！</td>
		</tr>
		<tr>
			<td align="right">组标识(英文)</td>
			<td><input type="text" name="cname" /></td>
			<td>请输入英文配置组标识！</td>
		</tr>
		<tr>
			<td align="right">排序</td>
			<td><input type="text" name="csort" value="0" /></td>
			<td>请输入排序数值！</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="isshow" value="1" />
				<input type="hidden" name="system" value="0" />
			</td>
			<td colspan="2">
				<button type="submit" class="button bg-sub radius-none">
					<i class="icon-edit"></i>
					提交保存
				</button>
				<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button radius-none">
					<i class="icon-th-list"></i>
					组 列 表
				</button>
			</td>
		</tr>
	</table>
</form>
</body>
</html>