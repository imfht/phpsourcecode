<?php

namespace App\Http\Utils;

use Illuminate\Support\Facades\Log;

class CommonUtil {
	public static function page_title($url) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13' );
		if (strpos ( $url, 'https://' ) !== false) {
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		}
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
		$fp = curl_exec ( $ch );
		curl_close ( $ch );
		
		if (! $fp) {
			Log::info ( "can not open" . $url );
			return '';
		}
		
		$res = preg_match ( "/<title>(.*)<\/title>/siU", $fp, $title_matches );
		if (! $res) {
			Log::info ( "can not preg" . $url );
			return '';
		}
		
		// Clean up title: remove EOL's and excessive whitespace.
		$title = preg_replace ( '/\s+/', ' ', $title_matches [1] );
		$title = trim ( $title );
		return $title;
	}
	public static function iftttnotify($title, $message, $url, $key) {
		if (empty ( $title ) || empty ( $message ) || empty ( $url )) {
			Log::info ( "params can not empty" );
			return false;
		}
		$post_params = array (
				'value1' => $title,
				'value2' => $message,
				'value3' => $url 
		);
		$headers = array (
				'Content-Type:application/json' 
		);
		
		// 创建连接
		$curl = curl_init ( 'https://maker.ifttt.com/trigger/montage/with/key/' . $key );
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_POST, true );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, json_encode ( $post_params ) );
		
		// 发送请求
		$response = curl_exec ( $curl );
		curl_close ( $curl );
		
		if (empty ( $response )) {
			Log::info ( "request error:" . $url );
			return false;
		}
		
		if (trim ( $response ) != "Congratulations! You've fired the montage event") {
			log::info ( 'request error:' . $response );
			return false;
		} else {
			return true;
		}
	}
	public static function shortUrl($url) {
		// 配置headers
		$headers = array (
				'Content-Type:application/json',
				'Token:7c9d6e49fbac124241c572eacfa14c77' 
		);
		
		// 创建连接
		$curl = curl_init ( 'https://dwz.cn/admin/v2/create' );
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $curl, CURLOPT_FAILONERROR, false );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_HEADER, false );
		curl_setopt ( $curl, CURLOPT_POST, true );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $curl, CURLOPT_POSTFIELDS, json_encode ( array (
				'url' => $url 
		) ) );
		
		// 发送请求
		$response = curl_exec ( $curl );
		curl_close ( $curl );
		
		if (empty ( $response )) {
			return false;
		}
		
		$result = json_decode ( $response, true );
		if ($result ['Code'] != '0') {
			return false;
		} else {
			return $result ['ShortUrl'];
		}
	}
	public static function isUrl($s) {
		return preg_match ( '/^http[s]?:\/\/' . '(([0-9]{1,3}\.){3}[0-9]{1,3}' . // IP形式的URL- 199.194.52.184
'|' . // 允许IP和DOMAIN（域名）
'([0-9a-z_!~*\'()-]+\.)*' . // 三级域验证- www.
'([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.' . // 二级域验证
'[a-z]{2,6})' . // 顶级域验证.com or .museum
'(:[0-9]{1,4})?' . // 端口- :80
'((\/\?)|' . // 如果含有文件对文件部分进行校验
'(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/', $s ) == 1;
	}
	public static function removeXSS($val) {
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$val = preg_replace ( '/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val );
		
		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=@avascript:alert('XSS')>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for($i = 0; $i < strlen ( $search ); $i ++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
			
			// @ @ search for the hex values
			$val = preg_replace ( '/(&#[xX]0{0,8}' . dechex ( ord ( $search [$i] ) ) . ';?)/i', $search [$i], $val ); // with a ;
			                                                                                                          // @ @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace ( '/(&#0{0,8}' . ord ( $search [$i] ) . ';?)/', $search [$i], $val ); // with a ;
		}
		
		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = Array (
				'javascript',
				'vbscript',
				'expression',
				'applet',
				'meta',
				'xml',
				'blink',
				'link',
				'style',
				'script',
				'embed',
				'object',
				'iframe',
				'frame',
				'frameset',
				'ilayer',
				'layer',
				'bgsound',
				'title',
				'base' 
		);
		$ra2 = Array (
				'onabort',
				'onactivate',
				'onafterprint',
				'onafterupdate',
				'onbeforeactivate',
				'onbeforecopy',
				'onbeforecut',
				'onbeforedeactivate',
				'onbeforeeditfocus',
				'onbeforepaste',
				'onbeforeprint',
				'onbeforeunload',
				'onbeforeupdate',
				'onblur',
				'onbounce',
				'oncellchange',
				'onchange',
				'onclick',
				'oncontextmenu',
				'oncontrolselect',
				'oncopy',
				'oncut',
				'ondataavailable',
				'ondatasetchanged',
				'ondatasetcomplete',
				'ondblclick',
				'ondeactivate',
				'ondrag',
				'ondragend',
				'ondragenter',
				'ondragleave',
				'ondragover',
				'ondragstart',
				'ondrop',
				'onerror',
				'onerrorupdate',
				'onfilterchange',
				'onfinish',
				'onfocus',
				'onfocusin',
				'onfocusout',
				'onhelp',
				'onkeydown',
				'onkeypress',
				'onkeyup',
				'onlayoutcomplete',
				'onload',
				'onlosecapture',
				'onmousedown',
				'onmouseenter',
				'onmouseleave',
				'onmousemove',
				'onmouseout',
				'onmouseover',
				'onmouseup',
				'onmousewheel',
				'onmove',
				'onmoveend',
				'onmovestart',
				'onpaste',
				'onpropertychange',
				'onreadystatechange',
				'onreset',
				'onresize',
				'onresizeend',
				'onresizestart',
				'onrowenter',
				'onrowexit',
				'onrowsdelete',
				'onrowsinserted',
				'onscroll',
				'onselect',
				'onselectionchange',
				'onselectstart',
				'onstart',
				'onstop',
				'onsubmit',
				'onunload' 
		);
		$ra = array_merge ( $ra1, $ra2 );
		
		$found = true; // keep replacing as long as the previous round replaced something
		while ( $found == true ) {
			$val_before = $val;
			for($i = 0; $i < sizeof ( $ra ); $i ++) {
				$pattern = '/';
				for($j = 0; $j < strlen ( $ra [$i] ); $j ++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra [$i] [$j];
				}
				$pattern .= '/i';
				$replacement = substr ( $ra [$i], 0, 2 ) . '__' . substr ( $ra [$i], 2 ); // add in <> to nerf the tag
				$val = preg_replace ( $pattern, $replacement, $val ); // filter out the hex tags
				if ($val_before == $val) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return $val;
	}
	public function auto_link_text($text) {
		$pattern = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
		$callback = create_function ( '$matches', '
	       $url       = array_shift($matches);
	 
	       $text = parse_url($url, PHP_URL_SCHEME) . "://" . parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH);
	 
	       return sprintf(\'<a rel="nowfollow" href="%s">%s</a>\', $url, $text);
	   ' );
		
		return preg_replace_callback ( $pattern, $callback, $text );
	}
	public static function formatTime($startTime, $endTime = '') {
		$format_time = '';
		if (! empty ( $startTime ) && ! empty ( $endTime )) {
			$return_str = '';
			$startFormat = date ( 'm月d日', strtotime ( $startTime ) );
			$endFormat = date ( 'm月d日', strtotime ( $endTime ) );
			
			if ($startFormat == $endFormat) {
				return $startFormat . ' ' . date ( 'h时i分', strtotime ( $startTime ) ) . '至' . date ( 'h时i分', strtotime ( $endTime ) );
			} else {
				return $startFormat . ' ' . date ( 'h时i分', strtotime ( $startTime ) ) . '至' . $endFormat . ' ' . date ( 'h时i分', strtotime ( $endTime ) );
			}
		} else if (! empty ( $startTime )) {
			return date ( 'Y年m月d日 h时i分', strtotime ( $startTime ) );
		} else {
			return false;
		}
	}
	public static function hostUrl($url) {
		$parts = parse_url ( $url );
		if (empty ( $parts ) || ! isset ( $parts ['scheme'] ) || ! isset ( $parts ['host'] )) {
			return false;
		} else {
			return $parts ['scheme'] . '://' . $parts ['host'];
		}
	}
	public static function formatContentHtml($content) {
		return CommonUtil::HtmlClose ( CommonUtil::stringRemoveXSS ( $content ) );
	}
	public static function HtmlClose($html) {
		preg_match_all ( '#<(?!meta|img|br|hr|inputb)b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result );
		$openedtags = $result [1];
		preg_match_all ( '#</([a-z]+)>#iU', $html, $result );
		$closedtags = $result [1];
		$len_opened = count ( $openedtags );
		if (count ( $closedtags ) == $len_opened) {
			return $html;
		}
		$openedtags = array_reverse ( $openedtags );
		for($i = 0; $i < $len_opened; $i ++) {
			if (! in_array ( $openedtags [$i], $closedtags )) {
				$html .= '</' . $openedtags [$i] . '>';
			} else {
				unset ( $closedtags [array_search ( $openedtags [$i], $closedtags )] );
			}
		}
		return $html;
	}
	public static function prettyDate($dateStr) {
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$halfamonth = $day * 15;
		$month = $day * 30;
		
		$diff = time () - strtotime ( $dateStr );
		if ($diff < 0) {
			return '';
		}
		
		$monthC = $diff / $month;
		$weekC = $diff / (7 * $day);
		$dayC = $diff / $day;
		$hourC = $diff / $hour;
		$minC = $diff / $minute;
		
		if ($monthC >= 1) {
			return ( int ) $monthC . "个月前";
		} else if ($dayC >= 1) {
			return ( int ) $dayC . '天前';
		} else if ($hourC >= 1) {
			return ( int ) $hourC . '个小时前';
		} else if ($minC >= 1) {
			return ( int ) $minC . '分钟前';
		} else {
			return '刚刚';
		}
	}
	public static function stringRemoveXSS($html) {
		preg_match_all ( "/\<([^\<]+)\>/is", $html, $ms );
		
		$searchs [] = '<';
		$replaces [] = '&lt;';
		$searchs [] = '>';
		$replaces [] = '&gt;';
		
		if ($ms [1]) {
			$allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote';
			$ms [1] = array_unique ( $ms [1] );
			foreach ( $ms [1] as $value ) {
				$searchs [] = "&lt;" . $value . "&gt;";
				
				$value = str_replace ( '&amp;', '_uch_tmp_str_', $value );
				$value = self::stringHtmlSpecialchars ( $value );
				$value = str_replace ( '_uch_tmp_str_', '&amp;', $value );
				
				$value = str_replace ( array (
						'\\',
						'/*' 
				), array (
						'.',
						'/.' 
				), $value );
				$skipkeys = array (
						'onabort',
						'onactivate',
						'onafterprint',
						'onafterupdate',
						'onbeforeactivate',
						'onbeforecopy',
						'onbeforecut',
						'onbeforedeactivate',
						'onbeforeeditfocus',
						'onbeforepaste',
						'onbeforeprint',
						'onbeforeunload',
						'onbeforeupdate',
						'onblur',
						'onbounce',
						'oncellchange',
						'onchange',
						'onclick',
						'oncontextmenu',
						'oncontrolselect',
						'oncopy',
						'oncut',
						'ondataavailable',
						'ondatasetchanged',
						'ondatasetcomplete',
						'ondblclick',
						'ondeactivate',
						'ondrag',
						'ondragend',
						'ondragenter',
						'ondragleave',
						'ondragover',
						'ondragstart',
						'ondrop',
						'onerror',
						'onerrorupdate',
						'onfilterchange',
						'onfinish',
						'onfocus',
						'onfocusin',
						'onfocusout',
						'onhelp',
						'onkeydown',
						'onkeypress',
						'onkeyup',
						'onlayoutcomplete',
						'onload',
						'onlosecapture',
						'onmousedown',
						'onmouseenter',
						'onmouseleave',
						'onmousemove',
						'onmouseout',
						'onmouseover',
						'onmouseup',
						'onmousewheel',
						'onmove',
						'onmoveend',
						'onmovestart',
						'onpaste',
						'onpropertychange',
						'onreadystatechange',
						'onreset',
						'onresize',
						'onresizeend',
						'onresizestart',
						'onrowenter',
						'onrowexit',
						'onrowsdelete',
						'onrowsinserted',
						'onscroll',
						'onselect',
						'onselectionchange',
						'onselectstart',
						'onstart',
						'onstop',
						// 'onsubmit','onunload','javascript','script','eval','behaviour','expression','style','class');
						'onsubmit',
						'onunload',
						'javascript',
						'script',
						'eval',
						'behaviour',
						'expression',
						'class' 
				);
				$skipstr = implode ( '|', $skipkeys );
				$value = preg_replace ( array (
						"/($skipstr)/i" 
				), '.', $value );
				if (! preg_match ( "/^[\/|\s]?($allowtags)(\s+|$)/is", $value )) {
					$value = '';
				}
				$replaces [] = empty ( $value ) ? '' : "<" . str_replace ( '&quot;', '"', $value ) . ">";
			}
		}
		$html = str_replace ( $searchs, $replaces, $html );
		
		return $html;
	}
	// php防注入和XSS攻击通用过滤
	public static function stringHtmlSpecialchars($string, $flags = null) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $val ) {
				$string [$key] = self::stringHtmlSpecialchars ( $val, $flags );
			}
		} else {
			if ($flags === null) {
				$string = str_replace ( array (
						'&',
						'"',
						'<',
						'>' 
				), array (
						'&amp;',
						'&quot;',
						'&lt;',
						'&gt;' 
				), $string );
				if (strpos ( $string, '&amp;#' ) !== false) {
					$string = preg_replace ( '/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string );
				}
			} else {
				if (PHP_VERSION < '5.4.0') {
					$string = htmlspecialchars ( $string, $flags );
				} else {
					if (! defined ( 'CHARSET' ) || (strtolower ( CHARSET ) == 'utf-8')) {
						$charset = 'UTF-8';
					} else {
						$charset = 'ISO-8859-1';
					}
					$string = htmlspecialchars ( $string, $flags, $charset );
				}
			}
		}
		
		return $string;
	}
}