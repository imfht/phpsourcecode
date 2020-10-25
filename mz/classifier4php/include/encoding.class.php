<?php
define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE) . chr(0xFF));
define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define ('UTF8_BOM' , chr(0xEF) . chr(0xBB) . chr(0xBF));
define ('TXT_PER_LINE', 50);

class encoding {
	//识别编码
	static function get_utf_encoding(&$text) {
		$first2 = substr($text, 0, 2);
		$first3 = substr($text, 0, 3);
		$first4 = substr($text, 0, 3);
		if ($first3 == UTF8_BOM) return 'UTF-8';
		elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
		elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
		elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
		elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
		return '';
	}
	//统一转换到utf8
	static function iconv($data, $output = 'utf-8') {
		$charset = self::get_utf_encoding($data);
		if($charset){
			return mb_convert_encoding($data, $output, $charset);
		}
		$encode_arr = array('UTF-8','ASCII','GBK','GB2312','BIG5', 'JIS','eucjp-win','sjis-win','EUC-JP');
		$encoded = mb_detect_encoding($data, $encode_arr);
		return mb_convert_encoding($data, $output, $encoded);
	}
}



?>