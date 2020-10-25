<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:40:58
         compiled from "./Application/Admin/View\Cat\add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1574563310faf3b328-06864895%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '289a4bd59a35b13668c04c313ebfc18a0f34c4a3' => 
    array (
      0 => './Application/Admin/View\\Cat\\add.tpl',
      1 => 1445948981,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1574563310faf3b328-06864895',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'select' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563310fb075f4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563310fb075f4')) {function content_563310fb075f4($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>添加分类</h3>
<form action="<?php echo @__CONTROLLER__;?>
/insert" method='post'>
	<div class="form-group">
	<label for="InputCat">上层分类：</label>
	<?php echo $_smarty_tpl->tpl_vars['select']->value;?>
 
	</div>
	<div class="form-group">
	<label for="InputName">分类名称：</label>
	<input type="text" class="form-control" name='name' value=''>
	</div>
	<button type="submit" class="btn btn-default">添加分类</button>
</form>
</div>
</div>

<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>