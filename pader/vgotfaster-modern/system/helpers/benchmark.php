<?php
/**
 * VgotFaster PHP Framework
 *
 * Benchmark Helpers
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * Calculate Microtime Used (seconds)
 *
 * @return Time used seconds
 */
if(!function_exists('microtimeUsed'))
{
	function microtimeUsed() {
		global $started;
		$now = microtime(true);
		return round($now - $started,7);
	}
}

/**
 * Get Memory Usage
 * (With unit KB)
 *
 * @return string memory usage
 */
if(!function_exists('memoryUsage'))
{
	function memoryUsage() {
		return round((memory_get_usage() / 1024),2).' KB';
	}
}
