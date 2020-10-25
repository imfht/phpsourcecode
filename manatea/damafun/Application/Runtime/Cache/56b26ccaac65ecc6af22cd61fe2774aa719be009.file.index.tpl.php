<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 18:46:10
         compiled from "./Application/Admin/View\Admin\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1681856373ef2e28944-81714169%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '56b26ccaac65ecc6af22cd61fe2774aa719be009' => 
    array (
      0 => './Application/Admin/View\\Admin\\index.tpl',
      1 => 1445949048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1681856373ef2e28944-81714169',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56373ef2f12f7',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56373ef2f12f7')) {function content_56373ef2f12f7($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>管理员信息</h3>
<table class="table">
	<th>管理员ID</th>
	<th>管理员</th>
	<th>操作</th>
	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['data']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
	<tr>
		<td><?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</td>
		<td><a href="<?php echo @__CONTROLLER__;?>
/delete/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
" onclick="return confirm('确定要删除吗？')">删除</a></td>
	</tr>
	<?php } ?>
</table>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>