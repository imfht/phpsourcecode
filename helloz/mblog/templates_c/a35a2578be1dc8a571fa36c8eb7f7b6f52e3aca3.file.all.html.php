<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-03 10:38:21
         compiled from "D:\wwwroot\blog\templates\all.html" */ ?>
<?php /*%%SmartyHeaderCode:1373054a616de481435-84100980%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a35a2578be1dc8a571fa36c8eb7f7b6f52e3aca3' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\all.html',
      1 => 1420277896,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1373054a616de481435-84100980',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a616de4be332_30709368',
  'variables' => 
  array (
    'all' => 0,
    'edit' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a616de4be332_30709368')) {function content_54a616de4be332_30709368($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel = "stylesheet" href = "./css/style.css" type = "text/css" />
<title>文章列表</title>
</head>
<body>
<div id = "top">
<div class = "logo">
<h1><a href = "../index.php">邹修平Blog</a></h1>
路漫漫其修远兮，吾将上下而求索。
</div>
<div id = "nav">
	<ul>
		<li><a href = "../index.php">首 页</a></li>
		<li><a href = "./index.php">所有文章</a></li>
		<li><a href = "http://www.xiaoz.me/" target = "_blank" rel = "nofollow">小z博客</a></li>
		<li><a href = "http://weibo.com/337003006" target = "_blank" rel = "nofollow">新浪微博</a></li>
		<li><a href = "./about.php">关 于</a></li>
	</ul>
</div>
</div>

<div id = "show">
	<div class = "all">
		<table width = "560">
		<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['value'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['value']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['all']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
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
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['all']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
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
				<td width = "360"><a href = "./article/?p=<?php echo $_smarty_tpl->tpl_vars['all']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" target = "_blank"><?php echo $_smarty_tpl->tpl_vars['all']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['title'];?>
</a></td>
				<td width = "96"><?php echo $_smarty_tpl->tpl_vars['all']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['date'];?>
</td>
				<?php if ($_smarty_tpl->tpl_vars['edit']->value==1) {?>
					<td>
						<a href = "../admin/edit.php?id=<?php echo $_smarty_tpl->tpl_vars['all']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" target = "_blank" rel = "nofollow" />编辑</a> | 
						<a href = "../admin/list.php?delete=<?php echo $_smarty_tpl->tpl_vars['all']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" rel = "nofollow">删除</a>
					</td>
				<?php }?>
			</tr>
			<?php endfor; endif; ?>
		<?php endfor; endif; ?>
		</table>
	</div>
</div>


<!-- 返回顶部 -->
<p id="back-to-top" style="display: block;"><a href="#top"><span></span>回到顶部</a></p>
<!-- 返回顶部END -->

<div id = "wrap">©2015 Powered by M-Blog | <a href = "./all.php" target = "_blank">站点地图</a> | 本站托管于<a href = "http://www.xiaoz.me/jump.php?id=03" target = "_blank" rel = "nofollow">恒创主机</a> </div>
</body>
</html><?php }} ?>
