<?php
/**
 * VgotFaster PHP Framework
 *
 * Time Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * 格式化时间戳
 *
 * @param int $seconds
 * @return
 */
if(!function_exists('formatTimestamp'))
{
	function formatTimestamp($seconds = '') {
		$VF =& getInstance();
		$lang = $VF->config->lang('time');

		if ($seconds == '') $seconds = 1;
		$str = '';
		$years = floor($seconds / 31536000);
		if ($years > 0) {
			$str .= $years." {$lang['years']}, ";
		}
		$seconds -= $years * 31536000;
		$months = floor($seconds / 2628000);
		if ($years > 0 || $months > 0) {
			if ($months > 0) {
				$str .= $months." {$lang['months']}, ";
			}
			$seconds -= $months * 2628000;
		}
		$weeks = floor($seconds / 604800);
		if ($years > 0 || $months > 0 || $weeks > 0) {
			if ($weeks > 0)	{
				$str .= $weeks." {$lang['weeks']}, ";
			}
			$seconds -= $weeks * 604800;
		}
		$days = floor($seconds / 86400);
		if ($months > 0 || $weeks > 0 || $days > 0) {
			if ($days > 0) {
				$str .= $days." {$lang['days']}, ";
			}
			$seconds -= $days * 86400;
		}
		$hours = floor($seconds / 3600);
		if ($days > 0 || $hours > 0) {
			if ($hours > 0) {
				$str .= $hours." {$lang['hours']}, ";
			}
			$seconds -= $hours * 3600;
		}
		$minutes = floor($seconds / 60);
		if ($days > 0 || $hours > 0 || $minutes > 0) {
			if ($minutes > 0) {
				$str .= $minutes." {$lang['minuents']}, ";
			}
			$seconds -= $minutes * 60;
		}
		if ($str == '') {
			$str .= $seconds." {$lang['seconds']}, ";
		}
		$str = substr(trim($str), 0, -1);
		return $str;
	}
}

//.