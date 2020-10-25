<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:40:53
         compiled from "./Application/Admin/View\Cat\mod.tpl" */ ?>
<?php /*%%SmartyHeaderCode:30081563310f53cc212-12621910%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a6bd63b0eddc33a9db6342abd8931a66db54ef46' => 
    array (
      0 => './Application/Admin/View\\Cat\\mod.tpl',
      1 => 1446124539,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30081563310f53cc212-12621910',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'select' => 0,
    'cats' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563310f5458c3',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563310f5458c3')) {function content_563310f5458c3($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>修改分类</h3>
	<form action="<?php echo @__CONTROLLER__;?>
/update" method="post">
		<div class="form-group">
		<label for="InputCat">请选择父类：</label>
		<?php echo $_smarty_tpl->tpl_vars['select']->value;?>
 
		</div>
		<input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['cats']->value['id'];?>
">
		<div class="form-group">
		<label for="InputName">修改类名</label>
			<input type="text" class="form-control" name='name' value='<?php echo $_smarty_tpl->tpl_vars['cats']->value['name'];?>
'>
		</div>
		<button type="submit" class="btn btn-default">修改分类</button>
	</form>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>