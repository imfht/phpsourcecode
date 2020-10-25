<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-02 10:33:55
         compiled from "D:\wwwroot\blog\templates\changepw.html" */ ?>
<?php /*%%SmartyHeaderCode:2777254a65638895858-90057228%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a1a8792b3438c28d049188a43b9a1e18139839fd' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\changepw.html',
      1 => 1420191229,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2777254a65638895858-90057228',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a656388c4650_52769162',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a656388c4650_52769162')) {function content_54a656388c4650_52769162($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel = "stylesheet" href = "../templates/css/new.css" />
<title>修改密码</title>

	<?php echo '<script'; ?>
>
		function check() {
			var new1 = myform.newpw1.value;
			var new2 = myform.newpw2.value;
			if(new1 == "" || new2 == "") {
				alert("新密码不能为空！");
				myform.newpw1.focus();
				return false;
			}
			if(new1 != new2) {
				alert("两次输入的密码不一致！");
				myform.newpw2.focus();
				return false;
			}
		}
	<?php echo '</script'; ?>
>

</head>
<body>
<div id = "change">
<form name = "myform" method = "post" action = "" onsubmit = "return check();">
	<table>
		<tr>
			<td width = "120">原密码：</td>
			<td><input type = "password" name = "oldpw" class = "input" /></td>
		</tr>
		<tr>
			<td>新密码：</td>
			<td><input type = "password" name = "newpw1" class = "input" /></td>
		</tr>
		<tr>
			<td>确认密码：</td>
			<td><input type = "password" name = "newpw2" class = "input" /></td>
		</tr>
		<tr>
			<td><input type = "submit" name = "sub" value = "确 认" class = "btn" /></td>
		</tr>
	</table>
</form>
</div>
</body>
</html><?php }} ?>
