<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 19:06:03
         compiled from "./Application/Home/View\Video\add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:527756334f1b3f3323-89557590%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7db9d818c85645edc94cd94c386c2af2aa211a02' => 
    array (
      0 => './Application/Home/View\\Video\\add.tpl',
      1 => 1446185464,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '527756334f1b3f3323-89557590',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'select' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56334f1b4ca0d',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56334f1b4ca0d')) {function content_56334f1b4ca0d($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="container">
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>用户视频上传</h3>
<form enctype="multipart/form-data" method="post" action="<?php echo @__CONTROLLER__;?>
/upload" onsubmit="$('.btn-upload').button('loading');">
	<div class="form-group">
	<label for="InputName">选择分类</label>
  <?php echo $_smarty_tpl->tpl_vars['select']->value;?>

   </div>
   <div class="form-group">
    <label for="InputVName">视频名称</label>
    <input type="text" class="form-control" id="InputVName" name="name" required >
  </div>
  <div class="form-group">
    <label for="InputName">上传人账号</label>
    <input type="text" class="form-control" id="InputName"  value="<?php echo $_SESSION['user']['name'];?>
" disabled>
    <input type="hidden" name="uid" value="<?php echo $_SESSION['user']['id'];?>
">
  </div>
  <div class="form-group">
    <label for="InputVideo">视频上传</label>
    <input type="file" id="InputVideo" name="path">
    <p class="help-block">文件上传最大限制为100M,建议mp4格式,目前支持mp4,avi,flv,wmv格式,视频转码时间较长，请您耐心等待</p>
  </div>
  <div class="form-group">
    <label for="InputName">描述</label>
    <textarea class="form-control" name="desn" rows="3"></textarea>
  </div>

  <button type="submit"  class="btn btn-default btn-upload">上传</button>
</form>
</div>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>