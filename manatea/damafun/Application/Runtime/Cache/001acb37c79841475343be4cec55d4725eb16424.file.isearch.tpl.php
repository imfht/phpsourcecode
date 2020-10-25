<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:14:07
         compiled from "./Application/Home/View\Index\isearch.tpl" */ ?>
<?php /*%%SmartyHeaderCode:912056330aaf1dc908-02745390%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '001acb37c79841475343be4cec55d4725eb16424' => 
    array (
      0 => './Application/Home/View\\Index\\isearch.tpl',
      1 => 1446185178,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '912056330aaf1dc908-02745390',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56330aaf3f7a8',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56330aaf3f7a8')) {function content_56330aaf3f7a8($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

  <div class="container">

    <div class="row">
      <div class="col-md-10 col-md-offset-1" style="padding:0px">
        <?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value){
$_smarty_tpl->tpl_vars["row"]->_loop = true;
?>
        <div class="row jumbotron1">
          <div class="media col-md-10  col-md-offset-1 ">
            <div class="media-left ">
              <a href="<?php echo @__MODULE__;?>
/video/index/vid/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">
                <img class="media-object" src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['row']->value['pic'];?>
" alt="未找到图片">
              </a>
            </div>
            <div class="media-body">
              <h4 class="media-heading"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
 </h4>
              <h5>发布时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['row']->value['ptime'],"%Y-%m-%d %H:%M:%S");?>
 点击量：<?php echo $_smarty_tpl->tpl_vars['row']->value['hot'];?>
 评论数：<?php echo $_smarty_tpl->tpl_vars['row']->value['comnumber'];?>
</h5>
              <h6>描述：<?php echo $_smarty_tpl->tpl_vars['row']->value['desn'];?>
</h6>
            </div>
          </div>
        </div>
        <?php }
if (!$_smarty_tpl->tpl_vars["row"]->_loop) {
?>
        <h4 class="col-md-7  col-md-offset-1">没有找到相关视频</h4>
        <?php } ?>
      </div>
    </div>

  
  </div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>