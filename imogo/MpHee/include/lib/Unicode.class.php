<?php
/**
 * 提供GBK,UTF8转化为Unicode编码,
 * Unicode转化为GBK,UTF8编码字符串类库
 * 
 * @author wang chong(wangchong1985@gmail.com)
 * @link http://www.wangchong.org
 * @version 1.0.0 (2011-04-15)
 * @package php-Unicode
 */
class Unicode
{
	/**
	 * 自定义str_to_unicode后的连接符
	 * @var string
	 */
	public $glue = "";

	/**
	 * 将字符串转换成unicode编码
	 *
	 * @param string $input
	 * @param string $input_charset
	 * @return string
	 */
	public function str_to_unicode($input, $input_charset = 'gbk')
	{
		$input = iconv($input_charset, "gbk", $input);
		preg_match_all("/[\x80-\xff]?./", $input, $ar);
		$b = array_map(array($this, 'utf8_unicode_'), $ar[0]);
		$outstr = join($this->glue, $b);
		return $outstr;
	}

	private function utf8_unicode_($c, $input_charset = 'gbk')
	{
		$c = iconv($input_charset, 'utf-8', $c);
		return $this->utf8_unicode($c);
	}

	// utf8 -> unicode
	private function utf8_unicode($c)
	{
		switch(strlen($c)) {
			case 1:
				//return $c;
				$n = ord($c[0]);
				break;
			case 2:
				$n = (ord($c[0]) & 0x3f) << 6;
				$n += ord($c[1]) & 0x3f;
				break;
			case 3:
				$n = (ord($c[0]) & 0x1f) << 12;
				$n += (ord($c[1]) & 0x3f) << 6;
				$n += ord($c[2]) & 0x3f;
				break;
			case 4:
				$n = (ord($c[0]) & 0x0f) << 18;
				$n += (ord($c[1]) & 0x3f) << 12;
				$n += (ord($c[2]) & 0x3f) << 6;
				$n += ord($c[3]) & 0x3f;
				break;
		}
		return "U+".base_convert($n, 10, 16);
	}

	/**
	 * 将unicode字符转换成普通编码字符
	 *
	 * @param string $str
	 * @param string $out_charset
	 * @return string
	 */
	public function str_from_unicode($str, $out_charset = 'gbk')
	{
		$str = preg_replace_callback("|U\+([0-9a-f]{1,4})|", array($this, 'unicode2utf8_'), $str);
		$str = iconv("UTF-8", $out_charset, $str);
		return $str;
	}

	private function unicode2utf8_($c)
	{
		return $this->unicode2utf8($c[1]);
	}

	private function unicode2utf8($c)
	{
		$c = base_convert($c, 16, 10);
		$str="";
		if ($c < 0x80) {
			$str.=chr($c);
		} else if ($c < 0x800) {
			$str.=chr(0xC0 | $c>>6);
			$str.=chr(0x80 | $c & 0x3F);
		} else if ($c < 0x10000) {
			$str.=chr(0xE0 | $c>>12);
			$str.=chr(0x80 | $c>>6 & 0x3F);
			$str.=chr(0x80 | $c & 0x3F);
		} else if ($c < 0x200000) {
			$str.=chr(0xF0 | $c>>18);
			$str.=chr(0x80 | $c>>12 & 0x3F);
			$str.=chr(0x80 | $c>>6 & 0x3F);
			$str.=chr(0x80 | $c & 0x3F);
		}
		return $str;
	}
}

