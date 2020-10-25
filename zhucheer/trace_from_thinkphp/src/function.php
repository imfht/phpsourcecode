<?php
use Cheer\TpTrace\ShowPageTrace;
/**
 * @author zhucheer
 * @since 2016-04-05
 * 
 * 为了要使用TP中的trace 主要用户自定义的函数
 */

function trace($string=' '){
	\Log::debug($string.' [TRACE]');
}

function showLogo(){
	$logo = \File::get(__DIR__.DIRECTORY_SEPARATOR.'logo.gif');
	return base64_encode($logo);
}