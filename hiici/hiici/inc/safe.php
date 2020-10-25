<?php

//check the token
if (!empty($_POST)) {
	//检查token
	if (!check_token()) die('非法的请求！^_^');
	//检查提交间隔
	if (1 > time() - @$_SESSION['TIME_POST_LAST']) die('提交的过于频繁！^_^');
	$_SESSION['TIME_POST_LAST'] = time();

}

//check the xss
if (!xss_check()) die;

//------------------------------------------------------->

//token 
function get_token() {
	$_SESSION['token'] = mt_rand();
	return $_SESSION['token'];
}

function check_token() {
	if ($_SESSION['token'] != intval(@$_REQUEST['token'])) return false;

	$_SESSION['token'] = mt_rand();
	return true;
}

/**
 * GET 参数跨站检测
 *
 * 增加对 CONTENT-TRANSFER-ENCODING 代码的检测 (IE MHTML 漏洞)
 */
function xss_check() {
	$temp = @strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
	if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
		return false;
	}

	return true;
}

//过滤img_url
function do_img_url_filter($img_url) {
	return preg_replace('/^#/', '',filter_var($img_url, FILTER_SANITIZE_URL));
}

/**
 * 过滤脚本代码
 * @param unknown $text
 * @return mixed
 */
function cleanjs($text) {
	$text = trim ( $text );
	//$text = stripslashes ( $text );
	// 完全过滤注释
	$text = @preg_replace ( '/<!--?.*-->/', '', $text );
	// 完全过滤动态代码
	$text = @preg_replace ( '/<\?|\?>/', '', $text );
	// 完全过滤js
	$text = @preg_replace ( '/<script?.*\/script>/', '', $text );
	// 过滤多余html
	$text = @preg_replace ( '/<\/?(html|head|meta|link|base|body|title|style|script|form|frame|frameset|math|maction|marquee)[^><]*>/i', '', $text );
	// 过滤on事件lang js
	while ( preg_match ( '/(<[^><]+)(data|onmouse|onexit|onclick|onkey|onsuspend|onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragend|ondragenter|ondragleave|ondragover|ondragstart|ondrop|onerror|onerrorupdate|onfilterchange|onfinish|onfocus|onfocusin|onfocusout|onhelp|onkeydown|onkeypress|onkeyup|onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|onmouseleave|onmousemove|onmouseout|onmouseover|onmouseup|onmousewheel|onmove|onmoveend|onmovestart|onpaste|onpropertychange|onreadystatechange|onreset|onresize|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|onunload)[^><]+/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], $mat [1], $text );
	}
	while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
		$text = str_replace ( $mat [0], $mat [1] . $mat [3], $text );
	}
	return $text;
}
