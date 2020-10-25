<?php /* Smarty version Smarty-3.1.21-dev, created on 2014-12-31 11:06:10
         compiled from "D:\wwwroot\blog\templates\seo.html" */ ?>
<?php /*%%SmartyHeaderCode:2481454a3b1c822d0c6-47572137%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '17d82f3feb78e96730f14e6c1530ace35a59aaf4' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\seo.html',
      1 => 1420020355,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2481454a3b1c822d0c6-47572137',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a3b1c8258049_34815402',
  'variables' => 
  array (
    'result' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a3b1c8258049_34815402')) {function content_54a3b1c8258049_34815402($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel = "stylesheet" href = "../templates/css/new.css" />
<title>SEO设置</title>
</head>
<body>
<div id = "seo">
<center>
<form name = "seo" method = "post" action = "">
	<table>
		<tr>
			<td width = "80">站点标题：</td>
			<td><input type = "text" name = "title" value = "<?php echo $_smarty_tpl->tpl_vars['result']->value['title'];?>
" class = "input" /></td>
		</tr>
		<tr>
			<td>副标题：</td>
			<td><input type = "text" name = "subtitle" value = "<?php echo $_smarty_tpl->tpl_vars['result']->value['subtitle'];?>
" class = "input" /></td>
		</tr>
		<tr>
			<td>关键词：</td>
			<td><input type = "text" name = "keywords" value = "<?php echo $_smarty_tpl->tpl_vars['result']->value['keywords'];?>
" class = "input" /></td>
		</tr>
		<tr>
			<td>描述：</td>
			<td>
				<textarea name = "description" rows = "4" cols = "60"><?php echo $_smarty_tpl->tpl_vars['result']->value['description'];?>
</textarea>
			</td>
		</tr>
		<tr>
			<td><input type = "submit" name = "save" value = "保 存" class = "sub" /></td>
		</tr>
	</table>
</form>
</center>
</div>
</body>
</html><?php }} ?>
