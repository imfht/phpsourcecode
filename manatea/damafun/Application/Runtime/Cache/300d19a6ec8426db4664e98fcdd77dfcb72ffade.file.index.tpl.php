<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:40:49
         compiled from "./Application/Admin/View\Cat\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28008563310f18ff587-41826873%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '300d19a6ec8426db4664e98fcdd77dfcb72ffade' => 
    array (
      0 => './Application/Admin/View\\Cat\\index.tpl',
      1 => 1445948994,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28008563310f18ff587-41826873',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563310f19a753',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563310f19a753')) {function content_563310f19a753($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>修改分类</h3>
<table class="table">
	<th>类名
	</th>
	<th colspan="2">操作</th>

	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['select']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</td>
			<td><a href="<?php echo @__CONTROLLER__;?>
/mod/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">修改</a></td>
			<td><a onclick="return confirm('你确定要删除分类吗？')" href="<?php echo @__CONTROLLER__;?>
/delete/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">删除</a></td>
		</tr>
	<?php } ?>
</table>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>