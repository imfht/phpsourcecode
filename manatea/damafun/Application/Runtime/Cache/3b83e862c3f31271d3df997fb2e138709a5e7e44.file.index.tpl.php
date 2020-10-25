<?php /* Smarty version Smarty-3.1.6, created on 2015-10-30 13:49:06
         compiled from "./Application/Admin/View\Login\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:25680563304d21d5543-24902351%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b83e862c3f31271d3df997fb2e138709a5e7e44' => 
    array (
      0 => './Application/Admin/View\\Login\\index.tpl',
      1 => 1445948942,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25680563304d21d5543-24902351',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'res' => 0,
    'url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_563304d282a83',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563304d282a83')) {function content_563304d282a83($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("public/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<body style="background:#555555; ">
<script type="text/javascript">
	$(function(){
		$('#book').hide();
		$('#book').fadeIn('normal');
	});
</script>
<div class="container"  id="#login">
    <div class="row" style="margin-top:100px">   
    <!-- <div class="col-md-6">
      <img src="<?php echo $_smarty_tpl->tpl_vars['res']->value;?>
/images/loginpic.jpg" style="width:400px;">
      </div>  -->
       <div class="col-md-4 col-md-offset-4">      
      <div class="panel panel-primary" id="book">
  <div class="panel-heading">请登录我的装逼系统</div>
  <div class="panel-body">
     <form class="form-horizontal" action="<?php echo @__CONTROLLER__;?>
/login" method="post">
        <label class="control-label" for="inputName"></label>
			<div class="controls">
			<div class="input-group">
			 <span class="input-group-addon" id="basic-addon1">
			 <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			 </span>
				<input id="inputName" name="username" type="text" class="form-control" placeholder="请输入用户名" required autofocus/>
				</div>
			</div>
			<label class="control-label" for="inputPassword"></label>
					<div class="controls">
					<div class="input-group">
			 <span class="input-group-addon" id="basic-addon1">
			 	<span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span>
			 </span>
						<input id="inputPassword" name="password" type="password" class="form-control"placeholder="请输入密码" required/>
						</div>
					</div>        
        <div class="controls">
         <label>
        <input type="checkbox" value="remember-me"> Remember me
         </label>
			<button type="submit" class="btn  btn-primary btn-block">有本事你点我啊</button>
		</div>  
		</form>     
      </div>
      </div>
</div>
</div>
      </div>
    </div> 
<!-- <div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<form class="form-horizontal" action="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/login" method="post">
				<div class="control-group">
					 <label class="control-label" for="inputName">用户名</label>
					<div class="controls">
						<input id="inputName" name="username" type="text" />
					</div>
				</div>
				<div class="control-group">
					 <label class="control-label" for="inputPassword">密码</label>
					<div class="controls">
						<input id="inputPassword" name="password" type="password" />
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						 <label class="checkbox"><input type="checkbox" /> Remember me</label> <button type="submit" class="btn">登陆</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div> -->
</body>
<?php echo $_smarty_tpl->getSubTemplate ("public/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>