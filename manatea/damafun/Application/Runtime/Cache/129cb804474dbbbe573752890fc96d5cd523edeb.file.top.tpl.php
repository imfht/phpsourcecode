<?php /* Smarty version Smarty-3.1.6, created on 2015-10-29 21:41:04
         compiled from "./Application/Admin/View\Index\top.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18157563221f0e48f97-48791859%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '129cb804474dbbbe573752890fc96d5cd523edeb' => 
    array (
      0 => './Application/Admin/View\\Index\\top.tpl',
      1 => 1446000928,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18157563221f0e48f97-48791859',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'app' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563221f101ffe',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563221f101ffe')) {function content_563221f101ffe($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<nav class="navbar navbar-default"  style="background:#e5e5e5;">
  <div class="container-fluid" >
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <a class="navbar-brand" href="#" style="color:#000;">后台管理界面</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#" style="color:#000;">您好，<?php echo $_SESSION['user']['name'];?>
用户</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?php echo @__MODULE__;?>
/login/logout" target="_top" style="color:#000;">登出</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!-- <div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<h3 class="text-center">
				h3. 这是一套可视化布局系统.
			</h3>
			<ul class="nav nav-tabs">
				<li class="active">
					您好，<?php echo $_SESSION['user']['name'];?>
用户
				</li>
<li>
					<a href="#">资料</a>
				</li>
				<li class="disabled">
					<a href="#">信息</a>
				</li>
				<li class=" pull-right">
					 <a href="<?php echo $_smarty_tpl->tpl_vars['app']->value;?>
/login/logout" target="_top">登出</a>
				</li>
			</ul>
		</div>
	</div>
</div> -->
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>