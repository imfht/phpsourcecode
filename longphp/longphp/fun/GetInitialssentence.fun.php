<?php
/**
 * 得到中文首字母，字符串是GBK编码
 * @param string $str
 * @return string
 */
 if(!defined('DIR')){
	exit('Please correct access URL.');
}
 
function getFirstLetter_GBK($str) {

	$asc = ord ( substr ( $str, 0, 1 ) );
	if ($asc < 160) {   //非中文
		if ($asc >= 48 && $asc <= 57) {
			return chr ( $asc ); // 数字
		} elseif ($asc >= 65 && $asc <= 90) {
			return chr ( $asc ); // A--Z
		} elseif ($asc >= 97 && $asc <= 122) {
			return chr ( $asc - 32 ); // a--z
		} else {
			return '~'; // 其他
		}
	} else {	// 中文
		$asc = $asc * 1000 + ord ( substr ( $str, 1, 1 ) );
		// 获取拼音首字母A--Z
		if ($asc >= 176161 && $asc < 176197) {
			return 'A';
		} elseif ($asc >= 176197 && $asc < 178193) {
			return 'B';
		} elseif ($asc >= 178193 && $asc < 180238) {
			return 'C';
		} elseif ($asc >= 180238 && $asc < 182234) {
			return 'D';
		} elseif ($asc >= 182234 && $asc < 183162) {
			return 'E';
		} elseif ($asc >= 183162 && $asc < 184193) {
			return 'F';
		} elseif ($asc >= 184193 && $asc < 185254) {
			return 'G';
		} elseif ($asc >= 185254 && $asc < 187247) {
			return 'H';
		} elseif ($asc >= 187247 && $asc < 191166) {
			return 'J';
		} elseif ($asc >= 191166 && $asc < 192172) {
			return 'K';
		} elseif ($asc >= 192172 && $asc < 194232) {
			return 'L';
		} elseif ($asc >= 194232 && $asc < 196195) {
			return 'M';
		} elseif ($asc >= 196195 && $asc < 197182) {
			return 'N';
		} elseif ($asc >= 197182 && $asc < 197190) {
			return 'O';
		} elseif ($asc >= 197190 && $asc < 198218) {
			return 'P';
		} elseif ($asc >= 198218 && $asc < 200187) {
			return 'Q';
		} elseif ($asc >= 200187 && $asc < 200246) {
			return 'R';
		} elseif ($asc >= 200246 && $asc < 203250) {
			return 'S';
		} elseif ($asc >= 203250 && $asc < 205218) {
			return 'T';
		} elseif ($asc >= 205218 && $asc < 206244) {
			return 'W';
		} elseif ($asc >= 206244 && $asc < 209185) {
			return 'X';
		} elseif ($asc >= 209185 && $asc < 212209) {
			return 'Y';
		} elseif ($asc >= 212209) {
			return 'Z';
		} else {
			return '~';
		}
	}
}

/**
 * 得到中文首字母，字符串是UTF8编码
 * @param string $str
 * @return string
 */
function getFirstLetter_UTF8($str) {
	
	return getFirstLetter_GBK(mb_convert_encoding($str, 'GBK'));
}

/**
 * 得到中文句子的首字符
 */
function getInitialsSentence_GBK($str) {
	$initials = '';
	for ($i = 0; $i < mb_strlen($str, 'GBK'); ++$i) {
		$initials .= getFirstLetter_GBK(mb_substr($str, $i, 1, 'GBK'));
	}
	return $initials;
}

/**
 * 得到中文句子的首字符
 */
function getInitialsSentence_UTF8($str) {
	
	return getInitialsSentence_GBK(mb_convert_encoding($str, 'GBK', 'UTF-8'));
}
