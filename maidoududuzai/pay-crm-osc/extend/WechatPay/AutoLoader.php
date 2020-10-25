<?php

/* WechatPaySdk */

define('WechatPay', preg_replace('/[\/\\\\]{1,}/', '/', __DIR__ . '/'));

spl_autoload_register('WechatPayLoader');

function WechatPayLoader($className)
{
	$className = preg_replace('/^\/*WechatPay\//', '', preg_replace('/[\/\\\\]{1,}/', '/', $className));
	$classFile = WechatPay . $className . '.php';
	if(is_file($classFile)) {
		include($classFile);
	}
}

