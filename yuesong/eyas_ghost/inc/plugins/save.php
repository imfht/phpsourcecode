<?php 

/*
 * 如果不是多语言网站，就不要加载语言包，可提升0.1s-0.5s的速度
 */
add_filter( 'locale', 'wpjam_locale' );
function wpjam_locale($locale) {
    $locale = ( is_admin() ) ? $locale : 'en_US';
    return $locale;
}

/*
 * 防止遭受恶意 URL 请求,与  Google Custom Search 有冲突
 */
if (strlen($_SERVER['REQUEST_URI']) > 255 ||
	strpos($_SERVER['REQUEST_URI'], "eval(") ||
	strpos($_SERVER['REQUEST_URI'], "base64")) {
		@header("HTTP/1.1 414 Request-URI Too Long");
		@header("Status: 414 Request-URI Too Long");
		@header("Connection: Close");
		@exit;
}