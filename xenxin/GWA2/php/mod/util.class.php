<?php
/* Util class, some swiss-knife like tools for the Webapp 
 * v0.1,
 * wadelau@ufqi.com,
 * Thu Aug 30 21:34:48 CST 2012
 * RELOCATED into comm/tools.function since 07:20 07 August 2016
 */

class Util{
				
	function __construct(){
		#				
	}

	static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	static function endsWith($haystack, $needle)
	{
			$length = strlen($needle);
			$start  = $length * -1; //negative
			return (substr($haystack, $start) === $needle);
	}



}


?>
