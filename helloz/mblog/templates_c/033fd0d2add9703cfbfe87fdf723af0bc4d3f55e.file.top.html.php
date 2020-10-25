<?php /* Smarty version Smarty-3.1.21-dev, created on 2014-12-29 13:01:33
         compiled from "D:\wwwroot\blog\templates\top.html" */ ?>
<?php /*%%SmartyHeaderCode:1343354a13dcf69dd44-60861172%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '033fd0d2add9703cfbfe87fdf723af0bc4d3f55e' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\top.html',
      1 => 1419854490,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1343354a13dcf69dd44-60861172',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a13dcf6d86d7_00768644',
  'variables' => 
  array (
    'user' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a13dcf6d86d7_00768644')) {function content_54a13dcf6d86d7_00768644($_smarty_tpl) {?><!doctype html>
<html>
<head>
<link rel = "stylesheet" href = "./css/style.css" type = "text/css" />
</head>
<body>
<div style = "width:100%;">
<div style = "width:960px;margin-left:5px;padding-left:28px;padding-top:5px;padding-bottom:5px;"> 
<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
您好，欢迎回来 | 
<a href = "../index.php" rel = "nofollow" target = "_parent">返回首页</a> |
<a href = "./admin/changepw.php" target = "show" rel = "nofollow">密码修改</a> | 
<a href = "./admin/info.php" target = "show" rel = "nofollow">个人资料</a> | 
<a href = "./admin.php?id=out" target="_parent">退出登录</a>
</div>
</div>
</body>
</html><?php }} ?>
