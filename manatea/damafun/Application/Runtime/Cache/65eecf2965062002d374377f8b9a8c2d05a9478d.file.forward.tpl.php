<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 18:43:09
         compiled from "./Application/Home/View\Index\forward.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2271356334886c37088-21329760%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '65eecf2965062002d374377f8b9a8c2d05a9478d' => 
    array (
      0 => './Application/Home/View\\Index\\forward.tpl',
      1 => 1446201779,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2271356334886c37088-21329760',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56334886e07e7',
  'variables' => 
  array (
    'nowcat' => 0,
    'scat' => 0,
    'row' => 0,
    'video' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56334886e07e7')) {function content_56334886e07e7($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


	<div class="container">

		<div class="row jumbotron1">
			<div class="col-md-7 col-md-offset-1" style="padding:0px">
				<div  style="padding:20px">
					<span><b>当前分类：</b><?php echo $_smarty_tpl->tpl_vars['nowcat']->value['name'];?>
</span>
					&nbsp;
					<b>子分类：</b>
					<?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['scat']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value){
$_smarty_tpl->tpl_vars["row"]->_loop = true;
?>
					&nbsp;<span><a href="<?php echo @__CONTROLLER__;?>
/forward/cat/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</a>
					<?php }
if (!$_smarty_tpl->tpl_vars["row"]->_loop) {
?>没有子分类
					</span>
					
					<?php } ?>
				</div>
			</div>
			<div class="col-md-3" style="padding:0px">
				<div  style="padding:20px">
					<span><b>UP主</b></span>
				</div>
			</div>
		</div>
		<?php  $_smarty_tpl->tpl_vars["row"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["row"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['video']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["row"]->key => $_smarty_tpl->tpl_vars["row"]->value){
$_smarty_tpl->tpl_vars["row"]->_loop = true;
?>
		<div class="row jumbotron1">
			<div class="media col-md-7  col-md-offset-1 ">
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
			<div class="col-md-3 " >
				<div  style="padding:20px">
					<span><b><?php echo $_smarty_tpl->tpl_vars['row']->value['uname'];?>
</b></span>
				</div>
			</div>
		</div>
		<?php }
if (!$_smarty_tpl->tpl_vars["row"]->_loop) {
?>
		<h4 class="col-md-7  col-md-offset-1">没有找到相关视频</h4>
		<?php } ?>
	
	</div>


<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>