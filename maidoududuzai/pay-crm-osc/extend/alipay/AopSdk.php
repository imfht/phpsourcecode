<?php

/* AopSdk */

define('AOP_SDK_DIR', preg_replace('/[\/\\\\]{1,}/', '/', __DIR__ . '/'));

require_once AOP_SDK_DIR . 'aop/AopClient.php';
require_once AOP_SDK_DIR . 'aop/AopCertClient.php';
require_once AOP_SDK_DIR . 'aop/AopCertification.php';

spl_autoload_register('AopSdkLoader');
function AopSdkLoader($className)
{
	$classFile = AOP_SDK_DIR . 'aop/request/' . $className . '.php';
	if(is_file($classFile)) {
		include($classFile);
	}
}

