<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('header'); ?>

<?php include T('error_box'); ?>

<h1><?php echo L('QQC 创建新用户'); ?></h1>

<div>
	<form action="<?php echo HomeUrl('index.php/QQConnect:create/'); ?>" method="post" >
		
		<label for="inputUsername"><?php echo L('用户名'); ?></label>
		<div>
			<input id="inputUsername" type="text" name="username" value="<?php echo $username; ?>" placeholder="<?php echo L('输入用户名'); ?>" />
			<p><?php echo L('输入用户名说明'); ?></p>
		</div>

		<label for="inputEmail"><?php echo L('邮箱'); ?></label>
		<div>
			<input id="inputEmail" type="text" name="email" value="" placeholder="example@domain.com" />
			<p><?php echo L('输入邮箱说明'); ?></p>
		</div>

		<label for="inputPassword1"><?php echo L('密码'); ?></label>
		<div>
			<input id="inputPassword1" type="password" name="password1" value="" placeholder="***" />
			<p><?php echo L('输入密码说明'); ?></p>
		</div>

		<label for="inputPassword2"><?php echo L('确认密码'); ?></label>
		<div>
			<input id="inputPassword2" type="password" name="password2" value="" placeholder="***" />
			<p><?php echo L('输入确认密码说明'); ?></p>
		</div>

		<label for="inputcaptcha"><?php echo L('验证码'); ?></label>
		<a href="<?php echo HomeUrl('index.php/QQConnect:create/'); ?>" title="<?php echo L('刷新'); ?>">
		<img src="<?php echo HomeUrl('Captcha.php'); ?>" />
		</a>
		<div>
			<input id="inputcaptcha" type="text" name="captcha" value="" />
			<p><?php echo L('输入验证码 说明'); ?></p>
		</div>
		<input type="submit" name="submit" value="<?php echo L('QQC 完成创建'); ?>" />
	</form>
</div>

<?php include T('footer'); ?>