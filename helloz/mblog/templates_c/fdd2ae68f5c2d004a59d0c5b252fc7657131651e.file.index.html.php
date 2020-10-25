<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-03 10:13:00
         compiled from "D:\wwwroot\blog\templates\index.html" */ ?>
<?php /*%%SmartyHeaderCode:316254a103aa6c2e40-88255939%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fdd2ae68f5c2d004a59d0c5b252fc7657131651e' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\index.html',
      1 => 1420276374,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '316254a103aa6c2e40-88255939',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a103aa6d2857_18084530',
  'variables' => 
  array (
    'seo' => 0,
    'ptitle' => 0,
    'arr' => 0,
    'up' => 0,
    'dn' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a103aa6d2857_18084530')) {function content_54a103aa6d2857_18084530($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset = "UTF-8" />
<link rel="stylesheet" href="./css/style.css?ab" type="text/css" media="screen" />
<?php echo '<script'; ?>
 src = "http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src = "./js/myjs.js"><?php echo '</script'; ?>
>
<title><?php echo $_smarty_tpl->tpl_vars['seo']->value['title'];
echo $_smarty_tpl->tpl_vars['ptitle']->value;?>
</title>
<meta name="keywords" content="<?php echo $_smarty_tpl->tpl_vars['seo']->value['keywords'];?>
" />
<meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['seo']->value['description'];?>
" />
</head>
<body>
<div id = "top">
<div class = "logo">
<h1><a href = "./index.php"><?php echo $_smarty_tpl->tpl_vars['seo']->value['title'];?>
</a></h1>
<?php echo $_smarty_tpl->tpl_vars['seo']->value['subtitle'];?>

</div>
<?php echo $_smarty_tpl->getSubTemplate ("./menu.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

</div>
<div id = "show">
	<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['value'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['value']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['value']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['arr']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
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
$_smarty_tpl->tpl_vars['smarty']->value['section']['n']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
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
	<div class = "zhaiyao">
		<div class = "title"><h2><a href = "./article/?p=<?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" title = "<?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['title'];?>
" target = "_blank"><?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['title'];?>
</a></h2></div>
	<div class = "info">作者：邹修平 | 发表于：<?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['date'];?>
</div>
	<?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['description'];?>
 [<a href = "./article/?p=<?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['id'];?>
" title = "<?php echo $_smarty_tpl->tpl_vars['arr']->value[$_smarty_tpl->getVariable('smarty')->value['section']['value']['index']]['title'];?>
" target = "_blank">阅读全文</a>]
	</div>
	<?php endfor; endif; ?>
	<?php endfor; endif; ?>
</div>
<div id = "page">
	<div class = "up"><a href = "./?p=<?php echo $_smarty_tpl->tpl_vars['up']->value;?>
" class = "fan"><<上一页</a></div>
	<div class = "dn"><a href = "./?p=<?php echo $_smarty_tpl->tpl_vars['dn']->value;?>
">下一页>></a></div>
	<div style = "clear:both;"></div>
</div>
<p id="back-to-top" style="display: block;"><a href="#top"><span></span></a></p>
<div id = "wrap">©2015 Powered by M-Blog | <a href = "./all.php" target = "_blank">站点地图</a> | 本站托管于<a href = "http://www.xiaoz.me/jump.php?id=03" target = "_blank" rel = "nofollow">恒创主机</a> </div>
</body>
</html><?php }} ?>
