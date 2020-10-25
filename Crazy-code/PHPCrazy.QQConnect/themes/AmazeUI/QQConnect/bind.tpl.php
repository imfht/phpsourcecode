<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('header'); ?>
	
	<?php if ($submit): include T('error_box'); endif; ?>
	
	<h1><?php echo L('QQC 绑定用户'); ?></h1>

	<div>
		<form action="<?php echo HomeUrl('index.php/QQConnect:bind/'); ?>" method="post">
			<label for="inputAccount"><?php echo L('帐号'); ?></label>
			<div>
				<input id="inputAccount" type="text" name="account" value="" placeholder="<?php echo L('邮箱 ID 用户名'); ?>" />
			</div>
			<label for="inputPassword"><?php echo L('密码'); ?></label>
			<div>
				<input id="inputPassword" type="password" name="password" value="" placeholder="<?php echo L('输入密码'); ?>" />
			</div>
			<input type="submit" name="submit" value="<?php echo L('QQC绑定'); ?>" />
		</form>
	</div>

<?php include T('footer'); ?>