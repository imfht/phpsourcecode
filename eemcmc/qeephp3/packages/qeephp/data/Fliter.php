<?php namespace qeephp\data;

/**
 * Fliter 类
 *
 * 过滤器 , 过滤用户输入的数据
 */
class Fliter
{
	/**
	 * Remove Invisible Characters
	 *
	 * Security::xss_clean() 有用到
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
 	static function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();

		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)

		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}

		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}

	/**
	 * 转换php编码
	 *
	 * @param	string
	 * @return	string
	 */
	function encode_php_tags($str)
	{
		return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}

	/**
	 * 去掉图片左右两边的标签, 只保留图片 url
	 *
	 * @param	string
	 * @return	string
	 */
	function strip_image_tags($str)
	{
		$str = preg_replace("#<img\s+.*?src\s*=\s*[\"'](.+?)[\"'].*?\>#", "\\1", $str);
		$str = preg_replace("#<img\s+.*?src\s*=\s*(.+?).*?\>#", "\\1", $str);

		return $str;
	}
}