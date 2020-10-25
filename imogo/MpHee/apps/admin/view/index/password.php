<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>密码管理</title>
<meta content="IE=8" http-equiv="X-UA-Compatible" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/font-awesome.css">
<script type="text/javascript" src="__PUBLIC__/js/do.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/config.js"></script>
</head>
<body>
<div id="contain">
<h2>修改密码：</h2>
<hr class="mb10"></hr>

<form enctype="multipart/form-data" onsubmit="return check_form(document.add);" method="post" action="">
	<div id="con_one_1" class="form_box">
		<table>
			<tr>
				<th>原密码：</th>
				<td><input class="input w200" type="text" name="oldpwd">必须填写</td>
			</tr>
			<tr>
				<th>新密码：</th>
				<td>
					<input class="input w200" type="text" name="newpwd1">必须填写
				</td>
			</tr>
			<tr>
				<th>再次输新密码：</th>
				<td>
					<input class="input w200" type="text" name="newpwd2">必须填写
				</td>
			</tr>
		</table>
	</div>
	<div class="btn">
		<input class="button" value="确定" type="submit">
        <input class="button" value="重置" type="reset">
	</div>
</form>
</div>
</body>
</html>
