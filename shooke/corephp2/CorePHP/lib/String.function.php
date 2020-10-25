<?php
/**
 * 字符串处理类
 * 魔术引号处理 cp_addslashes cp_stripslashes
 * 数据输入处理 cp_text cp_textarea cp_html
 * 字符编码 cp_iconv
 * 
 * 字符截取cp_substr cp_summary
 *
 * @author shooke <QQ:82523829>
 */

	/**
	 * url与GET传递数据进行编码
	 *
	 * cp_rawurlencode($data);
	 */
	function cp_rawurlencode($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = cp_rawurlencode ( $value );
			}
		} else {
			$data = rawurlencode ( $data );
		}
		return $data;
	}
	
	/**
	 * url与GET传递数据进行解码
	 *
	 * cp_rawurldecode($data);
	 */
	function cp_rawurldecode($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = cp_rawurldecode ( $value );
			}
		} else {
			$data = rawurldecode ( $data );
		}
		return $data;
	}
	/**
	 * url与GET传递数据进行编码
	 *
	 * cp_urlencode($data);
	 */
	function cp_urlencode($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = cp_urlencode ( $value );
			}
		} else {
			$data = urlencode ( $data );
		}
		return $data;
	}
	
	/**
	 * url与GET传递数据进行解码
	 *
	 * cp_urldecode($data);
	 */
	function cp_urldecode($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = cp_urldecode ( $value );
			}
		} else {
			$data = urldecode ( $data );
		}
		return $data;
	}
	
	/**
	 * 添加魔术引号，用于数据库录入
	 *
	 * cp_addslashes($data);
	 */
	function cp_addslashes($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = cp_addslashes ( $value );
			}
		} else {
			$data = addslashes ( $data );
		}
		return $data;
	}
	
	/**
	 * 去除魔术引号，用于还原输出cp_addslashes处理过的数据
	 *
	 * cp_stripslashes($data);
	 */
	function cp_stripslashes($data) {
		if (is_array ( $data )) {
			foreach ( $data as $key => $value ) {
				$data [$key] = cp_stripslashes ( $value );
			}
		} else {
			$data = stripslashes ( $data );
		}
		return $data;
	}
	
	/**
	 * 将html代码转换后输出主要是对<>
	 *
	 * cp_text('<br>');
	 * 输出&lt;br&gt;
	 */
	function cp_text($string) {
		return trim ( htmlspecialchars ( $string ) ); // 防止被挂马，跨站攻击
	}
	
	/**
	 * 多行文本
	 *
	 * cp_textarea($string);
	 */
	function cp_textarea($string) {
		$string = strip_tags ( $string, '<br>' );
		$string = str_replace ( " ", "&nbsp;", $string );
		$string = nl2br ( $string );
		return $string;
	}
	
	/**
	 * 编辑器数据允许html代码
	 * $text 原始字符串
	 * $tags 不过滤的标签
	 * cp_html($string);
	 * 
	 */
    function cp_html($string, $tags = null) {
    	$text	=	trim($string);
		//完全过滤注释
		$text = preg_replace('/<!--?.*-->/','',$text);
		//完全过滤动态代码
		$text = preg_replace('/<\?|\?'.'>/','',$text);		
		
		$text = preg_replace('@<style[^>]*?>.*?</style>@siU','',$text);
		$text = preg_replace('@<script[^>]*?>.*?</script>@si','',$text);
		$text = preg_replace('@<iframe[^>]*?>.*?</iframe>@siU','',$text);
		
		
		//过滤多余空格
		$text = str_replace('  ',' ',$text);
		return $text;
	}
	
	
	/**
	 * 移除Html代码中的XSS攻击
	 * 
	 * cp_remove_xss($val);
	 * 
	 */
	function cp_remove_xss($string) {
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$string = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $string);
	
		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=@avascript:alert('XSS')>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
	
			// @ @ search for the hex values
			$string = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $string); // with a ;
			// @ @ 0{0,7} matches '0' zero to seven times
			$string = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $string); // with a ;
		}
	
		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
		$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
	
		$found = true; // keep replacing as long as the previous round replaced something
		while ($found == true) {
			$string_before = $string;
			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
				$string = preg_replace($pattern, $replacement, $string); // filter out the hex tags
				if ($string_before == $string) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return $string;
	}
	/**
	 * ubb编码转换html	  
	 * cp_ubb($string);
	 *
	 */
	function cp_ubb($string) {
		$Text=trim($string);
		//$Text=htmlspecialchars($Text);
		$Text=preg_replace("/\\t/is","  ",$Text);
		$Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text);
		$Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text);
		$Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text);
		$Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text);
		$Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text);
		$Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text);
		$Text=preg_replace("/\[separator\]/is","",$Text);
		$Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text);
		$Text=preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$Text);
		$Text=preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$Text);
		$Text=preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\1</a>",$Text);
		$Text=preg_replace("/\[url\]([^\[]*)\[\/url\]/is","<a href=\"\\1\" target=_blank>\\1</a>",$Text);
		$Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text);
		$Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text);
		$Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text);
		$Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text);
		$Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text);
		$Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text);
		$Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text);
		$Text=preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis","color_txt('\\1')",$Text);
		$Text=preg_replace("/\[emot\](.+?)\[\/emot\]/eis","emot('\\1')",$Text);
		$Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text);
		$Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text);
		$Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text);
		$Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is"," <div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $Text);
		$Text=preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $Text);
		$Text=preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $Text);
		$Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div class='sign'>\\1</div>", $Text);
		$Text=preg_replace("/\\n/is","<br/>",$Text);
		return $Text;
	}
	
	/**
	 * 字符串截取
	 * @param unknown $str 字符串
	 * @param unknown $length 长度
	 * @param number $start 开始位置
	 * @param string $suffix 是否要...
	 * @param string $charset 编码
	 * @return string
	 */
	function cp_substr($string,  $length, $start=0, $suffix=false, $charset="utf-8") {
    	if(function_exists("mb_substr"))
    	$slice = mb_substr($string, $start, $length, $charset);
    	elseif(function_exists('iconv_substr')) {
    		$slice = iconv_substr($string,$start,$length,$charset);
    	}else{
    		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    		preg_match_all($re[$charset], $string, $match);
    		$slice = join("",array_slice($match[0], $start, $length));
    	}
    	return $suffix ? $slice.'...' : $slice;
    }
	
	/**
	 * 截取摘要：截断一段含有HTML代码的文本，但是不会出现围堵标记没有封闭的问题。
	 *
	 * cp_summary($string, 800);
	 * 
	 */	
	function cp_summary($text,$length=800){
		mb_regex_encoding("UTF-8");
		if(mb_strlen($text) <= $length ) return $text;
		$Foremost = mb_substr($text, 0, $length);
		$re = "<(\/?)(P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|TABLE|TR|TD|TH|INPUT|SELECT|TEXTAREA|OBJECT|A|UL|OL|LI|BASE|META|LINK|HR|BR|PARAM|IMG|AREA|INPUT|SPAN)[^>]*(>?)";
		$Single = "/BASE|META|LINK|HR|BR|PARAM|IMG|AREA|INPUT|BR/i";
	
		$Stack = array(); $posStack = array();
	
		mb_ereg_search_init($Foremost, $re, 'i');
	
		while($pos = mb_ereg_search_pos()){
			$match = mb_ereg_search_getregs();
			/*        [Child-matching Formulation]:
	
			$matche[1] : A "/" charactor indicating whether current "<...>" Friction is Closing Part
			$matche[2] : Element Name.
			$matche[3] : Right > of a "<...>" Friction
			*/
	
			if($match[1]==""){
				$Elem = $match[2];
				if(mb_eregi($Single, $Elem) && $match[3] !=""){
					continue;
				}
				array_push($Stack, mb_strtoupper($Elem));
				array_push($posStack, $pos[0]);
			}else{
				$StackTop = $Stack[count($Stack)-1];
				$End = mb_strtoupper($match[2]);
				if(strcasecmp($StackTop,$End)==0){
					array_pop($Stack);
					array_pop($posStack);
					if($match[3] ==""){
						$Foremost = $Foremost.">";
					}
				}
			}
		}
	
		$cutpos = array_shift($posStack) - 1;
		$Foremost =  mb_substr($Foremost,0,$cutpos,"UTF-8");
		if(mb_strlen($Foremost)<2) $Foremost = msubstr(strip_tags($text),0,$length);
		return $Foremost;
	}
	
	/**
	 * 编码转换
	 *
	 * cp_iconv($string, 'gbk','utf8');
	 * 
	 */	
	function cp_iconv($string,$from='gbk',$to='utf-8'){
		$from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
		$to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
		if( strtoupper($from) === strtoupper($to) || empty($string) || (is_scalar($string) && !is_string($string)) ){
			//如果编码相同或者非字符串标量则不转换
			return $string;
		}
		if(is_string($string) ) {
			if(function_exists('mb_convert_encoding')){
				return mb_convert_encoding ($string, $to, $from);
			}elseif(function_exists('iconv')){
				return iconv($from,$to,$string);
			}else{
				return $string;
			}
		}
		elseif(is_array($string)){
			foreach ( $string as $key => $val ) {
				$_key = cp_iconv($key,$from,$to);
				$string[$_key] = cp_iconv($val,$from,$to);
				if($key != $_key )
					unset($string[$key]);
			}
			return $string;
		}
		else{
			return $string;
		}
	}
	
	// 检查字符串是否是UTF8编码,是返回true,否则返回false
	function cp_is_utf8($string)
	{
		return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   		)*$%xs', $string);
	}

