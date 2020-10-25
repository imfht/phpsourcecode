<?php
/**
 * VgotFaster PHP Framework
 *
 * IP Address Helpers
 *
 * @package   VgotFaster
 * @author    pader
 * @copyright Copyright (c) 2009-2010, VGOT.NET
 * @link      http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

/**
 * Get IP Address
 *
 * @return string IP address
 */
if(!function_exists('ipAddress'))
{
	function ipAddress() {
		$VF =& getInstance();
		return $VF->input->ipAddress();
	}
}

/**
 * Get A Rand China IP(v4) Address
 *
 * @return string China IP address
 */
if(!function_exists('randChinaIp'))
{
	function randChinaIp() {
		$ips = array();
		$pre = array(1,27,58,59,60,61,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,134,159,161,162,166,167,168,169,175,180,182,183,192,198,202,203,210,218,219,220,221,222);
		$ips[] = $pre[array_rand($pre)];
		for ($i=0; $i<3; $i++) {
			$ips[] = round(rand(600000,2550000)/10000);
		}
		return join('.',$ips);
	}
}
