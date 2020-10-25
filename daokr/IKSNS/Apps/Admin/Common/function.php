<?php
/**
 * 系统后台非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function admin_md5($str, $key = 'ik_ucenter'){
	return '' === $str ? '' : md5(sha1($str) . $key);
}




