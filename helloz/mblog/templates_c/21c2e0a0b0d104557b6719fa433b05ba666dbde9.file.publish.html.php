<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-03 09:17:29
         compiled from "D:\wwwroot\blog\templates\publish.html" */ ?>
<?php /*%%SmartyHeaderCode:2633354a145b1a39685-67060115%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '21c2e0a0b0d104557b6719fa433b05ba666dbde9' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\publish.html',
      1 => 1420273043,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2633354a145b1a39685-67060115',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a145b1a68490_82616526',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a145b1a68490_82616526')) {function content_54a145b1a68490_82616526($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel="stylesheet" href="../css/style.css?x" type="text/css" media="screen" />
<?php echo '<script'; ?>
 src = "http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" charset="utf-8" src="../ueditor/ueditor.config.js?up"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" charset="utf-8" src="../ueditor/ueditor.all.min.js"> <?php echo '</script'; ?>
>
<title>邹修平博客</title>
</head>
<body>
<div id = "publish">
	<form name = "myform" method = "post" action = "" onsubmit = "return chkpub();">
	<table>
		<tr><td><input type = "text" name = "title" /><td></tr>
		<tr>
			<td><textarea name = "content"  id = "editor"></textarea></td>
		</tr>
		<tr>
			<td>关键词:</td>
		</tr>
		<tr>
			<td><input type = "text" name = "keywords" style = "width:320px;" /></td>
		</tr>
		<tr>
			<td>描述:</td>
		</tr>
		<tr>
			<td><textarea name = "description" rows = "3" cols = "80"></textarea></td>
		</tr>
		<tr>
			<td><input type = "submit" name = "sub" value = "发 表" class = "btn" /></td>
		</tr>
	</table>
	</form>
</div>
<?php echo '<script'; ?>
 type="text/javascript">
UE.getEditor('editor');//实例化编辑器
<?php echo '</script'; ?>
>
</body>
</html><?php }} ?>
