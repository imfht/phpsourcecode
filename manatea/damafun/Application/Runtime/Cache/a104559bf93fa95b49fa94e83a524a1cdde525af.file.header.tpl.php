<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:01:23
         compiled from "./Application/Home/View\public\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:300765633062215d147-66990510%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a104559bf93fa95b49fa94e83a524a1cdde525af' => 
    array (
      0 => './Application/Home/View\\public\\header.tpl',
      1 => 1446184881,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '300765633062215d147-66990510',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_5633062231e53',
  'variables' => 
  array (
    'row' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5633062231e53')) {function content_5633062231e53($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>CZFun</title>
	<link rel="stylesheet" type="text/css" href="<?php echo @APP_RES;?>
/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo @APP_RES;?>
/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo @APP_RES;?>
/css/style.css">
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/bootstrap-tooltip.js"></script>
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/bootstrap-dropdown.js"></script>
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/bootstrap-popover.js"></script>
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/bootstrap-carousel.js"></script>
	<script type="text/javascript" src="<?php echo @APP_RES;?>
/js/button.js"></script>
</head>
<body>
<!-- 头部 -->
	<div id="top"></div>
	<!-- 需要复制过去的中间布局部分 -->
	<!-- 需要显示的图片需要提前处理，否则显示的时候可能效果不好 -->
	</div>
		<div class="container-fluid"
		style="width: 100%; height: 200px; background: url(<?php echo @APP_RES;?>
/home/images/123.png); background-repeat:no-repeat;"></div>
				<!-- 此处是导航部分 -->
				<div class="container">
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<ul class="nav nav-pills" style="position: relative; top: 5px;">
					<li class><a href="<?php echo @__MODULE__;?>
/index/index">首页</a></li>
				</ul>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = ($_smarty_tpl->tpl_vars['cat']->value); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
					<li><a href="<?php echo @__MODULE__;?>
/index/forward/cat/<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['row']->value['name'];?>
</a></li>
					<?php } ?>
					<li><a href="<?php echo @__MODULE__;?>
/index/showCat">更多</a></li>
				</ul>
				<form class="navbar-form navbar-left" action="<?php echo @__MODULE__;?>
/index/isearch" method="GET" role="search">
					<div class="form-group">
						<input type="text" name="query" class="form-control" placeholder="Search"/>
					</div>
					<button type="submit" class="btn btn-primary">搜索</button>
				</form>
				<ul class="nav navbar-nav navbar-right">
					 <?php if ($_SESSION['userLogin']==0){?>
						 <li><a href="<?php echo @__MODULE__;?>
/user/login"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>登录</a></li>
						<li><a href="<?php echo @__MODULE__;?>
/user/register"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>注册</a></li>
					<?php }else{ ?>
						 <li><a href="#"><span class="glyphicon glyphicon-user" aria-hidden="true"></span><?php echo $_SESSION['user']['name'];?>
</a></li>
					<?php }?>
                    <?php if ($_SESSION['user']['allow']==1){?><li><a href="<?php echo @__MODULE__;?>
/video/add"><span class="glyphicon glyphicon-open" aria-hidden="true"></span>上传</a></li>
                    <?php }?>
                    <?php if ($_SESSION['userLogin']==1){?>
                                        <li><a href="<?php echo @__MODULE__;?>
/user/logout"><span class="glyphicon glyphicon-open" aria-hidden="true"></span>登出</a></li>
                    <?php }?>
				</ul>
			</div>
			</div>
		</nav>

		</div>
<?php }} ?>