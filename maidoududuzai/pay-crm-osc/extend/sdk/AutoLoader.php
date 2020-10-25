<?php

/* ExtSdk */

define('EXT_SDK_DIR', preg_replace('/[\/\\\\]{1,}/', '/', __DIR__ . '/'));

spl_autoload_register('ExtSdkLoader');

function ExtSdkLoader($className)
{
	$className = preg_replace('/^\/*/', '', preg_replace('/[\/\\\\]{1,}/', '/', $className));
	$classFile = EXT_SDK_DIR . $className . '.php';
	if(is_file($classFile)) {
		include($classFile);
	}
}

