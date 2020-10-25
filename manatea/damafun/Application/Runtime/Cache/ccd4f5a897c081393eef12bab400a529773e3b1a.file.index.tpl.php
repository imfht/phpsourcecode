<?php /* Smarty version Smarty-3.1.6, created on 2015-10-29 21:39:00
         compiled from "./Application/Admin/View\User\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24799563221746bed15-64644867%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ccd4f5a897c081393eef12bab400a529773e3b1a' => 
    array (
      0 => './Application/Admin/View\\User\\index.tpl',
      1 => 1445948908,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24799563221746bed15-64644867',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563221747ff25',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563221747ff25')) {function content_563221747ff25($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>用户权限管理</h3>
<table class="table">
	<th>用户ID</th>
	<th>用户名</th>
	<th>性别</th>
	<th>状态</th>
	<th>操作</th>

	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['user']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</td>
			<td><?php if ($_smarty_tpl->tpl_vars['row']->value['sex']==1){?>男<?php }else{ ?>女<?php }?></td>
			<td><?php if ($_smarty_tpl->tpl_vars['row']->value['allow']==1){?>正常<?php }else{ ?>冻结<?php }?></td>
			<td><a href="<?php echo @__CONTROLLER__;?>
/mod/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">管理</a></td>
		</tr>
	<?php } ?>
</table>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>