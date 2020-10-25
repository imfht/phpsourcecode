<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 18:32:57
         compiled from "./Application/Admin/View\Video\mod.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15833563710bae73c52-00298000%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfb0edbbb249a1a2a2f808d9c30f7f239f0983ab' => 
    array (
      0 => './Application/Admin/View\\Video\\mod.tpl',
      1 => 1446460372,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15833563710bae73c52-00298000',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563710bb00090',
  'variables' => 
  array (
    'data' => 0,
    'select' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563710bb00090')) {function content_563710bb00090($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>编辑视频</h3>
<form enctype="multipart/form-data" method="post" action="<?php echo @__CONTROLLER__;?>
/update">
<input type="hidden" name='id' value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id'];?>
">
	<div class="form-group">
	<label for="InputName">选择分类</label>
  <?php echo $_smarty_tpl->tpl_vars['select']->value;?>

   </div>
  <div class="form-group">
    <label for="InputVName">视频名称</label>
    <input type="text" class="form-control" id="InputVName" name="name" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
">
  </div>
  <div class="form-group">
    <label for="InputVName">视频点击量：<?php echo $_smarty_tpl->tpl_vars['data']->value['hot'];?>
</label>
  </div>
    <div class="form-group">
    <label for="InputVName">视频评论数：<?php echo $_smarty_tpl->tpl_vars['data']->value['comnumber'];?>
</label>
  </div>
    <div class="form-group">
    <label for="InputVName">上传时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['ptime'],"%Y-%m-%d %H:%M:%S");?>
</label>
  </div>
  <div class="form-group">
   <img src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['data']->value['pic'];?>
">

  </div>
  <div class="form-group">
    <label for="InputName">描述</label>
    <textarea class="form-control" name="desn" rows="3"><?php echo $_smarty_tpl->tpl_vars['data']->value['desn'];?>
</textarea>
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>