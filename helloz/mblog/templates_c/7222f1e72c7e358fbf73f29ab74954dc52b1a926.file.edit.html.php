<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-02 11:05:47
         compiled from "D:\wwwroot\blog\templates\edit.html" */ ?>
<?php /*%%SmartyHeaderCode:950354a55dbfc7d5b6-08700486%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7222f1e72c7e358fbf73f29ab74954dc52b1a926' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\edit.html',
      1 => 1420193140,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '950354a55dbfc7d5b6-08700486',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a55dbfcac3b0_88915049',
  'variables' => 
  array (
    'article' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a55dbfcac3b0_88915049')) {function content_54a55dbfcac3b0_88915049($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel = "stylesheet" href = "../templates/css/new.css" />
<?php echo '<script'; ?>
 type="text/javascript" charset="utf-8" src="../ueditor/ueditor.config.js?up"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" charset="utf-8" src="../ueditor/ueditor.all.min.js"> <?php echo '</script'; ?>
>
<title>修改<?php echo $_smarty_tpl->tpl_vars['article']->value['title'];?>
</title>
</head>
<body>

<?php echo '<script'; ?>
>
	function goback() {
		window.location.href = "./list.php";
	}
<?php echo '</script'; ?>
>

<div id = "publish">
<center>
	<form name = "edit" method = "post" action = "">
	<table>
		<tr><td><input type = "text" name = "title" value = "<?php echo $_smarty_tpl->tpl_vars['article']->value['title'];?>
"/><td></tr>
		<tr>
			<td><textarea name = "content"  id = "editor"><?php echo $_smarty_tpl->tpl_vars['article']->value['content'];?>
</textarea></td>
		</tr>
		<tr>
			<td>关键词:</td>
		</tr>
		<tr>
			<td><input type = "text" name = "keywords" style = "width:320px;" value = <?php echo $_smarty_tpl->tpl_vars['article']->value['keywords'];?>
 /></td>
		</tr>
		<tr>
			<td>描述:</td>
		</tr>
		<tr>
			<td><textarea name = "description" rows = "3" cols = "80"><?php echo $_smarty_tpl->tpl_vars['article']->value['description'];?>
</textarea></td>
		</tr>
		<tr>
			<td>
				<input type = "submit" name = "sub" value = "发 表" class = "btn" />  
				<input type = "submit" name = "back" value = "返 回" class = "btn" onclick = "goback();" />
			</td>
		</tr>
	</table>
	</form>
</center>
</div>
<?php echo '<script'; ?>
 type="text/javascript">
UE.getEditor('editor');//实例化编辑器
<?php echo '</script'; ?>
>
</body>
</html><?php }} ?>
