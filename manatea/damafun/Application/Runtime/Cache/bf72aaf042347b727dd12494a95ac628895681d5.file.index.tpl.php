<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:47:21
         compiled from "./Application/Admin/View\Video\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:25191563312798939c9-74178533%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf72aaf042347b727dd12494a95ac628895681d5' => 
    array (
      0 => './Application/Admin/View\\Video\\index.tpl',
      1 => 1445948059,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25191563312798939c9-74178533',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5633127999957',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5633127999957')) {function content_5633127999957($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>编辑视频</h3>
<table class="table">
	<th></th>
	<th>视频名</th>
	<th>上传时间</th>
	<th>上传用户</th>
	<th>视频类型</th>
	<th>点击量</th>
	<th>评论数量</th>
	<th colspan="2">操作</th>	

	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['data']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
		<tr>
			<td><img src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['row']->value['pic'];?>
"></td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</td>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['row']->value['ptime'],"%Y-%m-%d %H:%M:%S");?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['uname'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['pname'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['hot'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['row']->value['comnumber'];?>
</td>
			<td><a href="<?php echo @__CONTROLLER__;?>
/mod/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">修改</a></td>
			<td><a href="<?php echo @__CONTROLLER__;?>
/comment/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">查看评论</a></td>
			<td><a href="<?php echo @__CONTROLLER__;?>
/dama/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">弹幕管理</a></td>
			<td><a onclick="return confirm('你确定要删除该视频吗？')" href="<?php echo @__CONTROLLER__;?>
/delete/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
">删除</a></td>
			
		</tr>
	<?php } ?>
</table>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>