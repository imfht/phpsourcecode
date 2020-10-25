<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/metinfo.css" />
<style type="text/css">
input,textarea {
	border: 1px solid #CDCDCD;
	padding: 4px;
}

.error {
	border-color: red;
	background-color: #FFDDDD;
	color: red;
}

.pass {
	border-color: green;
	background-color: #DEFEE4;
	color: green;
}

.abnt {
	border: 1px solid black;
	padding: 3px;
	font-size: 12px;
}
*{
	font-family:consolas;
	font-size:12px;
}
</style>
</head>
<body>
	<div class="metinfotop">
		<div class="position">简体中文：网站后台</div>
		<div class="return"></div>
	</div>
	<div class="clear"></div>
		<table cellpadding="2" cellspacing="1" class="table" >
			<?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
				<td class="text"><?php echo ($key); ?>：</td>
				<td class="input"><span style="color: #1E71B1;font-weight:bold;font-style:italic"><?php echo ($vo); ?></span></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</table>
	</form>
</body>
</html>