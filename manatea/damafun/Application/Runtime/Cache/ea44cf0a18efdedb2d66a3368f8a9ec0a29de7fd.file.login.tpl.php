<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:13:46
         compiled from "./Application/Home/View\User\login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2415456330a9a0e1c38-41138404%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea44cf0a18efdedb2d66a3368f8a9ec0a29de7fd' => 
    array (
      0 => './Application/Home/View\\User\\login.tpl',
      1 => 1446185381,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2415456330a9a0e1c38-41138404',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56330a9a17a1e',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56330a9a17a1e')) {function content_56330a9a17a1e($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<div class="container">

		<div class="row">
      <div class="col-md-7 reg-pic">
      </div>
			<div class="col-md-4" style=" border:1px solid #cccccc; margin-top:20px;">
<div class="page-header">
<h2>登陆 CzFun</h2>
</div>
<form class="form-horizontal" method="post" action="<?php echo @__CONTROLLER__;?>
/loginCheck">				
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
    <div class="col-sm-7">
      <input type="text" class="form-control" id="inputEmail3" name="username" placeholder="请输入账号" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-7">
      <input type="password" class="form-control" name="password" id="inputPassword3" placeholder="请输入密码" required >
    </div>
  </div>
  <div class="col-sm-offset-2 col-sm-10" style="position:relative; left:20px;">
  	<a href="<?php echo @__MODULE__;?>
/user/register"><h6>>注册账号</h6></a>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10" style="position:relative; left:20px;"> 
      <button type="submit" class="btn btn-success" style="width:200px;"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>登陆</button>
    </div>
  </div>
</form>
			</div>
		</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>