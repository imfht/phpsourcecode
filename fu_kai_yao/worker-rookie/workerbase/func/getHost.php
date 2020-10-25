<?php 
namespace workerbase\func;
/**
 * 获取域名
 */
function getHost()
{
    $isSsl = false;
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        $isSsl = true;
    } elseif (isset($_SERVER['REQUEST_SCHEME']) && 'https' == $_SERVER['REQUEST_SCHEME']) {
        $isSsl = true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        $isSsl = true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']) {
        $isSsl = true;
    }

    $httpType = $isSsl ? 'https' : 'http';
	$domain = htmlspecialchars($httpType . '://' . $_SERVER['HTTP_HOST']);
	if(substr($domain, -1) != '/') {
        $domain .= '/';
	}
	return $domain;
}
