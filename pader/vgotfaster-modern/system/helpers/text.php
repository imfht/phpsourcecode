<?php
/**
 * VgotFaster PHP Framework
 *
 * Text Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * Cut String
 *
 * You can use to chinese string, but not UTF-8 encode
 *
 * @param string $str
 * @param int $cutleng
 * @param string $ct
 * @return
 */
if(!function_exists('cutstr'))
{
	function cutstr($str,$cutleng,$ct='..') {
		$strleng = strlen($str);
		if($cutleng > $strleng) return $str;
		$notchinanum = 0;
		for($i=0;$i<$cutleng;$i++) {
			if(ord(substr($str,$i,1))<=128) $notchinanum++;
		}
		if(($cutleng % 2 == 1) and ($notchinanum % 2 == 0)) $cutleng++;
		if(($cutleng % 2 == 0) and ($notchinanum % 2 == 1)) $cutleng++;
		return substr($str,0,$cutleng).$ct;
	}
}

/**
 * Obtain The Text Inside The HTML
 *
 * @param string $str HTML
 * @return
 */
if(!function_exists('html2text'))
{
	function html2text($str) {
		$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU",'',$str);
		$str = str_replace(array('<br />','<br>','<br/>'), "\n", $str);
		$str = strip_tags($str);
		return $str;
	}
}

/**
 * Print Array List As Text Table
 *
 * @param array $list
 * @param bool $return
 * @return string
 */
function printTextTable(array $list, $return=false) {
	$pad = 1; //Cell Left-Right Padding
	//Todo: Limit max cell width, if more, make new line
	//$maxCellWidth = 50;
	$build = $maxLen = array();

	foreach ($list as $i => $row) {
		//Info to build table
		$bi = array(
			'fields' => array(),
			'height' => 1
		);

		//遍历记录构建列表所需信息
		foreach ($row as $k => $v) {
			if (is_array($v)) {
				$v = print_r($v, true);
			}

			//Tab replace to 4 spaces
			if (strpos($v, "\t")) {
				$v = str_replace("\t", '    ', $v);
			}

			if (strpos($v, "\n") !== false) {
				$vlen = 0;
				$vlist = explode("\n", $v);
				foreach ($vlist as $segv) {
					$segvLen = strWidth($segv);
					$segvLen > $vlen && $vlen = $segvLen;
				}

				//Line height
				$height = count($vlist);
				if ($height > $bi['height']) {
					$bi['height'] = $height;
				}
				$bi['fields'][$k] = $vlist;
			} else {
				$vlen = strWidth($v);
				$bi['fields'][$k] = array($v);
			}

			//Field max width
			if (!isset($maxLen[$k]) || $vlen > $maxLen[$k]) {
				$maxLen[$k] = $vlen;
			}
		}

		$build[$i] = $bi;
	}

	unset($list);

	//Build header
	$headLine = '+';
	$leftPad = str_repeat(' ', $pad);
	$line = '|';

	foreach ($maxLen as $k => $v) {
		//Check length, if key length big than value, use ke length
		$klen = strlen($k);
		if ($klen > $v) {
			$v = $klen;
		}

		$fl = $v + $pad * 2; //With pad sum length
		$maxLen[$k] = $fl; //Change all keys length to pad sum length

		$headLine .= str_repeat('-', $fl) .'+';
		$line .= $leftPad.$k.str_repeat(' ', $fl - $klen - $pad).'|';
	}

	$txt = $headLine."\n".$line."\n".$headLine."\n";

	//build body
	foreach ($build as $bi) {
		for ($i=0; $i<$bi['height']; $i++) {
			$txt .= '|';

			foreach ($bi['fields'] as $k => $v) {
				if (isset($v[$i])) {
					$txt .= $leftPad.$v[$i].str_repeat(' ', $maxLen[$k] - strWidth($v[$i]) - $pad).'|';
				} else {
					$txt .= str_repeat(' ', $maxLen[$k]).'|';
				}
			}

			$txt .= "\n";
		}
		$txt .= $headLine."\n";
	}

	if ($return) {
		return $txt;
	} else {
		echo $txt;
	}
}

/**
 * Count String Width
 *
 * Count string readable width, english char is 1 width, chinese word is 2 width.
 *
 * @param string $str
 * @return int
 */
function strWidth($str) {
	$len = strlen($str);
	$i = $count = 0;
	while ($i < $len) {
		if (preg_match('/^['.chr(0xa1).'-'.chr(0xff).']+$/', $str[$i])) {
			$i += 3;
			$count += 2;
		} else {
			$i += 1;
			$count += 1;
		}
	}
	return $count;
}
