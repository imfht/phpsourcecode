<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->_var['charset']; ?>" />
<title>您需要登录后才能使用本功能</title>

<link href="templates/style/zx/font/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!--[if IE 7]>
<link rel="stylesheet" href="templates/style/zx/font/css/font-awesome-ie7.min.css">
<![endif]-->
<link href="templates/style/zx/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery.js'; ?>" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'ecmall.js'; ?>" charset="utf-8"></script>
</head>
<script type="text/javascript">
if (self != top)
{
    /* 在框架内，则跳出框架 */
    top.location = self.location;
}
$(function(){
    $('#user_name').focus();
});
</script>
<body>
<div class="container">
	<div class="up"></div>
	<div class="down">
	<div class="footer">
		<div class="ver">本系统由360cd.cn免费提供全部技术支持</div>
	</div>
		
	</div>
	<div class="box">
		
		<div class="main">
		<div class="title">ECMOS电商后台管理系统</div>
			<form method="post">
			<div class='zx-form-group'>
				<label for="用户名">用户名</label>
				<div class="zx-form-input"><input type="text" name="user_name" placeholder="管理员用户名"></div>
			</div>
			<div class='zx-form-group'>
				<label for="密&nbsp;&nbsp;&nbsp;码">密&nbsp;&nbsp;&nbsp;码</label>
				<div class="zx-form-input"><input type="password" name="password" placeholder="管理员密码"></div>
			</div>
			<?php if ($this->_var['captcha']): ?>
			<div class='zx-form-group'>
				<label for="验证码">验证码</label>
				<div class="zx-form-input"><input type="text" name="captcha" class="checkcode"> <div class="validates"><img onclick="this.src='index.php?app=captcha&' + Math.round(Math.random()*10000)" style="cursor:pointer;" class="validate" src="index.php?app=captcha&<?php echo $this->_var['random_number']; ?>" /></div></div>
			</div>
			
			<?php endif; ?>
			<div class='zx-form-group'><br>
				<div class="zx-form-input"><input type="submit" value="提交"></div>
			</div>
		</form>
		</div>
		

	</div>
</div>
</body>
</html>