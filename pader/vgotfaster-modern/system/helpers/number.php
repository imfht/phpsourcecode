<?php
/**
 * VgotFaster PHP Framework
 *
 * Number Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2012, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * Convert File Size With Unit
 *
 * @param float $size
 * @return string File size with unit
 */
if(!function_exists('formatFilesize'))
{
	function formatFilesize($filesize) {
		$filesizename = array('Bytes','KB','MB','GB','TB','PB','EB','ZB','YB');
		return $filesize ? number_format($filesize/pow(1024, ($i = floor(log($filesize, 1024)))), 2, '.', '') . $filesizename[$i] : '0 Bytes';
	}
}

/**
 * Probability Boolean
 *
 * @param int $p The number between 1 to 100
 * @return boolean
 */
if(!function_exists('probability'))
{
	function probability($p) {
		$one = rand(1,100);
		$total = range(1,100);
		shuffle($total);
		$range = array_slice($total,0,$p);
		return in_array($one,$range);
	}
}

/**
 * Integer To Binary String
 *
 * @param int $int
 * @param int $len
 * @return binary bytes
 */
if (!function_exists('int2byte'))
{
	function int2byte($int, $len=4) {
		$max = pow(256,$len) - 1;
		if ($int > $max) {
			$debugTrace = current(debug_backtrace());
			trigger_error(__FUNCTION__."() Integer value $int can't big then $len bytes max value $max, called on line {$debugTrace['line']}");
			exit;
		}
		$bytes = '';
		for ($i=0; $i<$len; $i++) {
			$bytes .= chr($int >> (8 * $i) & 255);
		}
		return $bytes;
	}
}

/**
 * Binary String To Integer
 *
 * @param $bytesData binary bytes
 * @return int
 */
if (!function_exists('byte2int')) 
{
	function byte2int($bytesData) {
		$len = strlen($bytesData);
		$value = 0;
		for ($i=0; $i<$len; $i++) {
			$value = $value | ord($bytesData{$i}) << (8 * $i);
		}
		return $value;
	}
}

//.