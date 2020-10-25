<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 18:46:11
         compiled from "./Application/Admin/View\Admin\add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2585956373ef371feb8-02989756%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9195fbff60f81dcf4a7a6f5ee887c2af44c7c8fb' => 
    array (
      0 => './Application/Admin/View\\Admin\\add.tpl',
      1 => 1445949030,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2585956373ef371feb8-02989756',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56373ef37a8a5',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56373ef37a8a5')) {function content_56373ef37a8a5($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>添加管理员</h3>
<form  method="post" action="<?php echo @__CONTROLLER__;?>
/insert">

  <div class="form-group">
    <label for="InputVName">管理员名称</label>
    <input type="text" class="form-control" id="InputVName" name="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
">
  </div>

  <div class="form-group">
    <label for="InputVName">管理员密码</label>
    <input type="password" class="form-control" id="InputPassword" name="password" >
  </div>

   <div class="form-group">
    <label for="InputVName">确认密码</label>
    <input type="password" class="form-control" id="InputRPword" name="repassword" >
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>