<?php
/*
*	Package:		PHPCrazy.QQConnect
*	Link:			http://git.oschina.net/Crazy-code/PHPCrazy.QQConnect
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/ include T('header'); ?>
	<p><?php echo L('第一次登录 说明'); ?></p>
	<p><a href="<?php echo HomeUrl('index.php/QQConnect:create/'); ?>"><?php echo L('QQC 创建新用户'); ?></a></p>
	<p><a href="<?php echo HomeUrl('index.php/QQConnect:bind/'); ?>"><?php echo L('QQC 绑定用户'); ?></a></p>
<?php include T('footer'); ?>