<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>修改列表</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
	<button type="button" onClick="location.href='<?php echo U('index');?>'" class="button border">
		<i class="icon-th-list"></i>
		钩子列表
	</button>
	<button type="button" onClick="location.href='<?php echo U('add');?>'" class="button border bg">
		<i class="icon-edit"></i>
		修改钩子
	</button>
</div>
<div class="bg border padding margin-top">修改钩子</div>
<form action="<?php echo U('edit');?>" method="POST">
	<table class="table table-bordered table-hover table-condensed table-responsive">
		<tr>
			<td align="right" width="200">钩子名称</td>
			<td><input type="text" name="name" value="<?php echo $field['name'];?>"/></td>
		</tr>
		<tr>
			<td align="right">钩子描述</td>
			<td><textarea name="description" cols="20" rows="3"><?php echo $field['description'];?></textarea></td>
		</tr>
		<tr>
			<td align="right">状态</td>
			<td>
				<label><input type="radio" name="status" value="1"     <?php if($field['status']==1){ ?>checked="checked"<?php } ?>>开启</label>
				<label><input type="radio" name="status" value="0"     <?php if($field['status']==0){ ?>checked="checked"<?php } ?>>关闭</label>
			</td>
		</tr>
		<tr>
			<td align="right">钩子类型</td>
			<td>
				<select name="type">
					<option value="1"     <?php if($field['type']==1){ ?>selected="selected"<?php } ?>>控制器</option>
					<option value="0"     <?php if($field['type']==0){ ?>selected="selected"<?php } ?>>视图</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type="hidden" name="id" value="<?php echo $hd['get']['id'];?>" /></td>
			<td><button class="button border bg-sub">确 定</button></td>
		</tr>
	</table>
</form>
</body>
</html>