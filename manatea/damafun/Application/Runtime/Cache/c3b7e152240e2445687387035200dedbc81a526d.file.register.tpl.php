<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 14:13:59
         compiled from "./Application/Home/View\User\register.tpl" */ ?>
<?php /*%%SmartyHeaderCode:528356330aa7e42bd7-32646211%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3b7e152240e2445687387035200dedbc81a526d' => 
    array (
      0 => './Application/Home/View\\User\\register.tpl',
      1 => 1446185410,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '528356330aa7e42bd7-32646211',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_56330aa7efa58',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56330aa7efa58')) {function content_56330aa7efa58($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body>
<div class="container">

		<div class="row">
			<div class="col-md-7 reg-pic">
			</div>
			<div class="col-md-4" style=" border:1px solid #cccccc; margin-top:20px;">
<div class="page-header">
<h2>注册 CzFun</h2>
</div>
<form class="form-horizontal" method="post" action="<?php echo @__CONTROLLER__;?>
/registerAction">				

  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputPassword3" class="col-sm-3 control-label">用户名</label>
    <div class="col-sm-7">
      <input type="text" name="name" class="form-control" id="inputPassword3" placeholder="请输入用户名" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputEmail3" class="col-sm-2 control-label">密码</label>
    <div class="col-sm-7">
      <input type="password" name="password" class="form-control" id="inputEmail3" placeholder="请输入密码" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputEmail3" class="col-sm-3 control-label">确认密码</label>
    <div class="col-sm-7">
      <input type="password" name="repassword" class="form-control" id="inputEmail3" placeholder="请输入确认密码" required >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:20px;">
    <label for="inputEmail3" class="col-sm-2 control-label">邮箱</label>
    <div class="col-sm-7">
      <input type="email" name="email" class="form-control" id="inputEmail3" >
    </div>
  </div>
  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputEmail3" class="col-sm-3 control-label">性别</label>
    <div class="col-sm-7">
       <div class="radio">
        <label>
          <input type="radio" name="sex" id="optionsRadios1" value="0" checked>
         男
        </label>
        <label>
          <input type="radio" name="sex" id="optionsRadios2" value="1">
          女
        </label>
      </div>
    </div>
  </div>
  <div class="form-group" style="position:relative; left:-15px;">
    <label for="inputEmail3" class="col-sm-3 control-label">联系方式</label>
    <div class="col-sm-7">
      <input type="tel" name="tel" class="form-control" id="inputtel" pattern="[0-9]{11}" title="11位验证号码">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10" style="position:relative; left:20px;"> 
      <button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>注册</button>
      <button type="submit" class="btn btn-default" >返回登陆</button>
    </div>
  </div>
</form>
			</div>
		</div>
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>