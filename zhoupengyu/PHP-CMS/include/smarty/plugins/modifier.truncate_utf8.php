<?php
/**
* UTF8 helper functions
*
* @license LGPL (http://www.gnu.org/copyleft/lesser.html)
* @author Andreas Gohr 
*/ 

if(!defined('UTF8_MBSTRING')){
	if(function_exists('mb_substr') && !defined('UTF8_NOMBSTRING')){
		define('UTF8_MBSTRING',1);
	}else{
		define('UTF8_MBSTRING',0);
	}
}

if(UTF8_MBSTRING){ mb_internal_encoding('UTF-8'); }

function utf8_strlen($string){
	return strlen(utf8_decode($string));
}

function utf8_substr($str, $offset, $length = null) {
	if(UTF8_MBSTRING){
		if( $length === null ){
			return mb_substr($str, $offset);
		}else{
			return mb_substr($str, $offset, $length);
		}
	}

	$str = (string)$str;
	$offset = (int)$offset;
	if (!is_null($length)) $length = (int)$length;
	
	if ($length === 0) return '';
	if ($offset < 0 && $length < 0 && $length < $offset) return '';
	
	$offset_pattern = '';
	$length_pattern = '';
	
	if ($offset < 0) {
		$strlen = strlen(utf8_decode($str));
		$offset = $strlen + $offset;
		if ($offset < 0) $offset = 0;
	}
	
	if ($offset > 0) {
		$Ox = (int)($offset/65535);
		$Oy = $offset%65535;
	
		if ($Ox) $offset_pattern = '(?:.{65535}){'.$Ox.'}';
			$offset_pattern = '^(?:'.$offset_pattern.'.{'.$Oy.'})';
		} else {
			$offset_pattern = '^';
		}
	
		if (is_null($length)) {
			$length_pattern = '(.*)$';
		} else {
	
			if (!isset($strlen)) $strlen = strlen(utf8_decode($str));
			if ($offset > $strlen) return '';
			
			if ($length > 0) {
			
			$length = min($strlen-$offset, $length);
			
			$Lx = (int)($length/65535);
			$Ly = $length%65535;
			
			if ($Lx) $length_pattern = '(?:.{65535}){'.$Lx.'}';
			$length_pattern = '('.$length_pattern.'.{'.$Ly.'})';
		
		} else if ($length < 0) {
	
			if ($length < ($offset - $strlen)) return '';
			
			$Lx = (int)((-$length)/65535);
			$Ly = (-$length)%65535;
			
			if ($Lx) $length_pattern = '(?:.{65535}){'.$Lx.'}';
			$length_pattern = '(.*)(?:'.$length_pattern.'.{'.$Ly.'})$';
		}
	}
	if (!preg_match('#'.$offset_pattern.$length_pattern.'#us',$str,$match)) return '';
	return $match[1];
}

function smarty_modifier_truncate_utf8($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
{
	if ($length == 0)
	{
		return '';
	}
	
	if(utf8_strlen($string) > $length)
	{
		$length -= utf8_strlen($etc);
		//return utf8_substr($string, 0, $length+1);
		if(!$break_words && !$middle)
		{
//			$string = preg_replace('/\s+?(\S+)?$/us', '', utf8_substr($string, 0, $length+1));
			$string = trim(utf8_substr($string, 0, $length+1));
		}
		
		if(!$middle)
		{
			return utf8_substr($string, 0, $length) . $etc;
		}else{
			return utf8_substr($string, 0, $length / 2) . $etc . utf8_substr($string, -$length / 2);
		}
	}else{
		return $string;
	}
}
?>
