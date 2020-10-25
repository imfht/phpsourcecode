<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 15:29:03
         compiled from "./Application/Admin/View\Video\comment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9118563710bf793128-78162807%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e8a95480aedf7c41edeaa3f3dd5b147181a3741' => 
    array (
      0 => './Application/Admin/View\\Video\\comment.tpl',
      1 => 1445948834,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9118563710bf793128-78162807',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'video' => 0,
    'cat' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563710bf898cd',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563710bf898cd')) {function content_563710bf898cd($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'C:\\Lamp\\apache24\\htdocs\\damafun\\ThinkPHP\\Library\\Vendor\\Smarty\\plugins\\modifier.date_format.php';
?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="row">
<div class="col-md-10 col-md-offset-1">
<h3>视频信息</h3>
<table class="table">
	<th></th>
	<th>视频名</th>
	<th>上传时间</th>
	<th>视频类型</th>
	<th>点击量</th>
	<th>评论数量</th>	
		<tr>
			<td><img src="<?php echo @APP_RES;?>
/uploads/images/<?php echo $_smarty_tpl->tpl_vars['video']->value['pic'];?>
"></td>
			<td><?php echo $_smarty_tpl->tpl_vars['video']->value['name'];?>
</td>
			<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['video']->value['ptime'],"%Y-%m-%d %H:%M:%S");?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['cat']->value['name'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['video']->value['hot'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['video']->value['comnumber'];?>
</td>
			
		</tr>
</table>
<h3>评论</h3>
<table class="table">
	<th>用户id</th>
	<th>用户名</th>
	<th>评论时间</th>
	<th></th>	
	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['data']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
	<tr>
		<td><?php echo $_smarty_tpl->tpl_vars['row']->value['uid'];?>
</td>
		<td><a href="<?php echo @__APP__;?>
/user/mod/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['uid'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</a></td>
		<td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['row']->value['time'],"%Y-%m-%d %H:%M:%S");?>
</td>
		<td></td>
	</tr>
	<tr><th colspan="4">评论：</td></tr>
	<tr>
		<td colspan="3"><?php echo $_smarty_tpl->tpl_vars['row']->value['comment'];?>
</td>
		<td><a href="<?php echo @__CONTROLLER__;?>
/delcom/id/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
/vid/<?php echo $_smarty_tpl->tpl_vars['video']->value['id'];?>
" onclick="return confirm('确认删除该条评论吗')">删除</a>
		</td>
	</tr>
	<?php } ?>
</table>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>