<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 19:10:15
         compiled from "./Application/Home/View\Index\showCat.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1857156330c938efee4-26621081%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1319a4bf9a307329c01d78f80f57a561f678d1e0' => 
    array (
      0 => './Application/Home/View\\Index\\showCat.tpl',
      1 => 1446203414,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1857156330c938efee4-26621081',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56330c939f1c2',
  'variables' => 
  array (
    'data' => 0,
    'row' => 0,
    'key' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56330c939f1c2')) {function content_56330c939f1c2($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="container">

        <?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value){
$_smarty_tpl->tpl_vars["row"]->_loop = true;
?>
        <div class="row catlistsheet" >
          <div class="col-md-10  col-md-offset-1 ">
          	<div class="jumbotron1">
          	<a href="<?php echo @__CONTROLLER__;?>
/forward/cat/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</a>
          	</div>
          	<?php  $_smarty_tpl->tpl_vars["key"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["key"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value['video']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["key"]->key => $_smarty_tpl->tpl_vars["key"]->value){
$_smarty_tpl->tpl_vars["key"]->_loop = true;
?>
          	<div class="jumbotron1 col-md-offset-1">
	          <div class="media catlistmedia">
	            <div class="media-left ">
	              <a href="<?php echo @__MODULE__;?>
/video/index/vid/<?php echo $_smarty_tpl->tpl_vars['key']->value['id'];?>
">
	                <img class="media-object" src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['key']->value['pic'];?>
" alt="未找到图片">
	              </a>
	            </div>
	            <div class="media-body">
	              <h4 class="media-heading"><?php echo $_smarty_tpl->tpl_vars['key']->value['name'];?>
 </h4>
	              <h5>发布时间：<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['key']->value['ptime'],"%Y-%m-%d %H:%M:%S");?>
 点击量：<?php echo $_smarty_tpl->tpl_vars['key']->value['hot'];?>
 评论数：<?php echo $_smarty_tpl->tpl_vars['key']->value['comnumber'];?>
</h5>
	              <h6>描述：<?php echo $_smarty_tpl->tpl_vars['key']->value['desn'];?>
</h6>
	            </div>
	          </div> 
	          </div>
	          <?php }
if (!$_smarty_tpl->tpl_vars["key"]->_loop) {
?>
      		  <h4>该分类尚未拥有视频</h4>
	          <?php } ?>
          </div>
        </div>
        <?php } ?>
    </div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>