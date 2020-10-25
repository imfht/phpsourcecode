<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 15:29:10
         compiled from "./Application/Admin/View\Video\dama.tpl" */ ?>
<?php /*%%SmartyHeaderCode:32383563710c6225ee1-88125312%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9f7384730438a307a7286006081d4c6cdb273158' => 
    array (
      0 => './Application/Admin/View\\Video\\dama.tpl',
      1 => 1445948851,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32383563710c6225ee1-88125312',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'row' => 0,
    'vid' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563710c62be48',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563710c62be48')) {function content_563710c62be48($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>弹幕</h3>
<table class="table">
	<th>弹幕</th>
	<th>发送时间</th>
	<th>用户名</th>
	<th>操作</th>
	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['data']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value[0];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value[1][0];?>
s</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value[1][8];?>
</td>
			<td><a onclick="return confirm('你确定要删除该弹幕吗？')" href="<?php echo @__CONTROLLER__;?>
/deldama/time/<?php echo $_smarty_tpl->tpl_vars['row']->value[1][0];?>
/vid/<?php echo $_smarty_tpl->tpl_vars['vid']->value;?>
">删除弹幕</a></td>
		</tr>
	<?php } ?>
</table>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>