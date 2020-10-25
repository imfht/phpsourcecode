<?php /* Smarty version Smarty-3.1.6, created on 2015-10-29 21:41:04
         compiled from "./Application/Admin/View\Index\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:25624563221f091cae7-60991580%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1ef6d7f5968b2612b3d592639c5fa4e084d0a109' => 
    array (
      0 => './Application/Admin/View\\Index\\index.tpl',
      1 => 1446009700,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25624563221f091cae7-60991580',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563221f09fb59',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563221f09fb59')) {function content_563221f09fb59($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

 <frameset rows="50,*" frameborder=0 id="scrool">
	<frame src="<?php echo @__CONTROLLER__;?>
/top" name="top" />

	<frameset cols="180,*">
		<frame src="<?php echo @__CONTROLLER__;?>
/left" name="left"/>
			
		<frame src="<?php echo @__MODULE__;?>
/Video/Index" name="main"/>
		
	</frameset>
	
</frameset> 
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<?php }} ?>