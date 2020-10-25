<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 15:32:37
         compiled from "./Application/Admin/View\Video\add.tpl" */ ?>
<?php /*%%SmartyHeaderCode:159485637119543c7d9-34611035%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '13b802bf731a795fab3d731025fc3af5f22d4041' => 
    array (
      0 => './Application/Admin/View\\Video\\add.tpl',
      1 => 1445946994,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '159485637119543c7d9-34611035',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'url' => 0,
    'select' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56371195584a2',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56371195584a2')) {function content_56371195584a2($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>添加视频</h3>
<form enctype="multipart/form-data" method="post" action="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/insert">
	<div class="form-group">
	<label for="InputName">选择分类</label>
  <?php echo $_smarty_tpl->tpl_vars['select']->value;?>

   </div>
   <div class="form-group">
    <label for="InputVName">视频名称</label>
    <input type="text" class="form-control" id="InputVName" name="name">
  </div>
  <div class="form-group">
    <label for="InputName">上传人账号</label>
    <input type="email" class="form-control" id="InputName"  value="<?php echo $_SESSION['user']['name'];?>
" disabled>
    <input type="hidden" name="uid" value="<?php echo $_SESSION['user']['uid'];?>
">
  </div>
  <div class="form-group">
    <label for="InputVideo">视频上传</label>
    <input type="file" id="InputVideo" name="path">
    <p class="help-block">文件上传最大限制为500M,目前支持mp4,avi,flv,wmv格式</p>
  </div>
  <div class="form-group">
    <label for="InputName">描述</label>
    <textarea class="form-control" name="desn" rows="3"></textarea>
  </div>

  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>