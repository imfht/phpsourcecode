<?php /* Smarty version Smarty-3.1.6, created on 2015-10-29 21:39:02
         compiled from "./Application/Admin/View\User\mod.tpl" */ ?>
<?php /*%%SmartyHeaderCode:27018563221766fdc46-22327358%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27e69e1a12cf9dcc813b202ea0ae3591362e611e' => 
    array (
      0 => './Application/Admin/View\\User\\mod.tpl',
      1 => 1445948923,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27018563221766fdc46-22327358',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56322176878b2',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56322176878b2')) {function content_56322176878b2($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>用户</h3>
  <div class="form-group">
    <label>
      用户ID:<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>

    </label>
  </div>
  <div class="form-group">
    <label for="InputVName">用户姓名：<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
</label>
  </div>
    <div class="form-group">
    <label for="InputVName">性别：<?php if ($_smarty_tpl->tpl_vars['user']->value['sex']==1){?>男<?php }else{ ?>女<?php }?></label>
  </div>
  <div class="form-group">
    <label for="InputVName">爱好：<?php if ($_smarty_tpl->tpl_vars['user']->value['hobby']==''){?>空<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['user']->value['hobby'];?>
<?php }?></label>
  </div>
  <div class="form-group">
    <label for="InputVName">电话：<?php if ($_smarty_tpl->tpl_vars['user']->value['tel']==''){?>空<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['user']->value['tel'];?>
<?php }?></label>
  </div>
  <div class="form-group">
    <label for="InputVName">电子邮箱：<?php if ($_smarty_tpl->tpl_vars['user']->value['email']==''){?>空<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['user']->value['email'];?>
<?php }?></label>
  </div>
  <div class="form-group">
    <label for="InputVName">状态：<?php if ($_smarty_tpl->tpl_vars['user']->value['allow']==1){?>正常<br><a href="<?php echo @__CONTROLLER__;?>
/allow/allow/1/id/<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
">冻结</a><?php }else{ ?>冻结<br><a href="<?php echo @__CONTROLLER__;?>
/allow/allow/0/id/<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
">恢复</a><?php }?></label>
  </div>

</form>
</div>
</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>