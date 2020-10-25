<?php /* Smarty version Smarty-3.1.6, created on 2015-11-02 18:34:23
         compiled from "./Application/Admin/View\Index\left.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10270563221f15fffc5-03894018%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8643efd473ddae92bd1d6075251f9b3306df25b4' => 
    array (
      0 => './Application/Admin/View\\Index\\left.tpl',
      1 => 1446460428,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10270563221f15fffc5-03894018',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563221f17a5e2',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563221f17a5e2')) {function content_563221f17a5e2($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body  style="background:#f5f5f5;">

<div class="accordion" id="accordion2" >
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" style="cursor:pointer;"><h4>视频管理</h4></ul>
    </div>
    <div id="collapseOne" class="accordion-body collapse in">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
<!--       <li role="presentation"><a href="<?php echo @__MODULE__;?>
/video/add" target="main">添加视频</a></li> -->
	      <li role="presentation"><a href="<?php echo @__MODULE__;?>
/video/index" target="main">编辑视频</a></li>
	      </ul>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseTwo" style="cursor:pointer;"><h4>分类管理</h4></ul>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
     
	      <li role="presentation"><a href="<?php echo @__MODULE__;?>
/cat/add" target="main">添加分类</a></li>
	      <li role="presentation"><a href="<?php echo @__MODULE__;?>
/cat/index" target="main">修改分类</a></li> 
	      </ul>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseThree" style="cursor:pointer;"><h4>用户管理</h4></ul>
    </div>
    <div id="collapseThree" class="accordion-body collapse">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
     <li role="presentation"><a href="<?php echo @__MODULE__;?>
/user/index" target="main">用户权限</a></li> 
	      </ul>
      </div>
    </div>
  </div>
  <?php if ($_SESSION['user']['allow']==1){?>
  <div class="accordion-group">
    <div class="accordion-heading">
    <ul class="nav nav-tabs nav-stacked" data-toggle="collapse" data-parent="#accordion1" href="#collapseFour" style="cursor:pointer;"><h4>管理员权限</h4></ul>
    </div>
    <div id="collapseFour" class="accordion-body collapse">
      <div class="accordion-inner">
        <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
     <li role="presentation"><a href="<?php echo @__MODULE__;?>
/admin/index" target="main">管理员管理</a></li> 
     <li role="presentation"><a href="<?php echo @__MODULE__;?>
/admin/add" target="main">添加管理员</a></li> 
        </ul>
      </div>
    </div>
  </div>
  <?php }?>
  </div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>