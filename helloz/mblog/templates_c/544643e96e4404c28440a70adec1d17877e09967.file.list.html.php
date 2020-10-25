<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-01 16:33:48
         compiled from "D:\wwwroot\blog\templates\list.html" */ ?>
<?php /*%%SmartyHeaderCode:580154a3d86dc3ad17-44999437%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '544643e96e4404c28440a70adec1d17877e09967' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\list.html',
      1 => 1420126423,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '580154a3d86dc3ad17-44999437',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a3d86dd34d42_84320348',
  'variables' => 
  array (
    'list' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a3d86dd34d42_84320348')) {function content_54a3d86dd34d42_84320348($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel = "stylesheet" href = "../templates/css/new.css" />
<title>文章列表</title>
</head>
<body>
<div id = "list">
<table>
<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['value'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['value']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['list']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['name'] = 'value';
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['value']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['value']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['value']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['value']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['value']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['value']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['value']['total']);
?>
<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['n'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['n']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['name'] = 'n';
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['max'] = (int) 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show'] = true;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['max'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['n']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['n']['total']);
?>
	<tr>
		<td>
			<a href = "../article/?p=<?php echo $_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" target = "_blank" style = "color:#282828;"><?php echo $_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['title'];?>
</a> 
		</td>
		<td>
			[<?php echo $_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['date'];?>
]
		</td>
		<td>
			<a href = "./edit.php?id=<?php echo $_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" rel = "nofollow">编辑</a> 
			<a href = "?delete=<?php echo $_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" rel = "nofollow">删除</a>
		</td>
	</tr>
<?php endfor; endif; ?>
<?php endfor; endif; ?>
</table>
</div>
</body>
</html><?php }} ?>
