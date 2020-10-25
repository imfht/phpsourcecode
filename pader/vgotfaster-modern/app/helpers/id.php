<?php

/**
 * Translates a number to a short alhanumeric version
 *
 * Translated any number up to 9007199254740992
 * to a shorter version in letters e.g.:
 * 9007199254740989 --> PpQXn7COf
 *
 * specifiying the second argument true, it will
 * translate back e.g.:
 * PpQXn7COf --> 9007199254740989
 *
 * this function is based on any2dec && dec2any by
 * fragmer[at]mail[dot]ru
 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
 *
 * If you want the alphaID to be at least 3 letter long, use the
 * $pad_up = 3 argument
 *
 * In most cases this is better than totally random ID generators
 * because this can easily avoid duplicate ID's.
 * For example if you correlate the alpha ID to an auto incrementing ID
 * in your database, you're done.
 *
 * The reverse is done because it makes it slightly more cryptic,
 * but it also makes it easier to spread lots of IDs in different
 * directories on your filesystem. Example:
 * $part1 = substr($alpha_id,0,1);
 * $part2 = substr($alpha_id,1,1);
 * $part3 = substr($alpha_id,2,strlen($alpha_id));
 * $destindir = "/".$part1."/".$part2."/".$part3;
 * // by reversing, directories are more evenly spread out. The
 * // first 26 directories already occupy 26 main levels
 *
 * more info on limitation:
 * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
 *
 * if you really need this for bigger numbers you probably have to look
 * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
 * or: http://theserverpages.com/php/manual/en/ref.gmp.php
 * but I haven't really dugg into this. If you have more info on those
 * matters feel free to leave a comment.
 *
 * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
 * @author  Simon Franz
 * @author  Deadfish
 * @author  SK83RJOSH
 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
 * @link    http://kevin.vanzonneveld.net/
 *
 * @param mixed   $in   String or long input to translate
 * @param boolean $to_num  Reverses translation when true
 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
 * @param string  $pass_key Supplying a password makes it harder to calculate the original ID
 *
 * @return mixed string or long
 */
function alphaId($in, $to_num = false, $pad_up = false, $pass_key=null) {
	$out   =   null;
	$index = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$base  = strlen($index);

	//按照密钥打乱索引
	if ($pass_key !== null) {
		for ($n = 0; $n < $base; $n++) $i[] = substr($index, $n, 1);

		$pass_hash = hash('sha256', $pass_key);
		$pass_hash = strlen($pass_hash) < $base ? hash('sha512', $pass_key) : $pass_hash;

		for ($n = 0; $n < $base; $n++) {
			$p[] =  substr($pass_hash, $n, 1);
		}

		array_multisort($p, SORT_DESC, $i);
		$index = implode($i);
	}

	if ($to_num) {
		// Digital number  <<--  alphabet letter code
		$len = strlen($in) - 1;

		for ($t = $len; $t >= 0; $t--) {
			$bcp = bcpow($base, $len - $t);
			$out += strpos($index, substr($in, $t, 1)) * $bcp;
		}

		if (is_numeric($pad_up)) {
			$pad_up--;

			if ($pad_up > 0) {
				$out -= pow($base, $pad_up);
			}
		}
	} else {
		// Digital number  -->>  alphabet letter code
		if (is_numeric($pad_up)) {
			$pad_up--;

			if ($pad_up > 0) {
				$in += pow($base, $pad_up);
			}
		}

		for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
			$bcp = bcpow($base, $t);
			$a   = floor($in / $bcp);
			$out .= substr($index, $a, 1);
			$in  = $in - ($a * $bcp);
		}
	}

	return $out;
}

function packId($id) {
	$str = base64_encode(pack('d', $id));
	$str = str_replace(array('/', '+'), array('-', '_'), $str);
	return rtrim($str, '=');
}

function unpackId($str) {
	$str = str_pad($str, 12, '=');
	$str = str_replace(array('-', '_'), array('/', '+'), $str);
	$arr = @unpack('d', base64_decode($str));
	return $arr ? sprintf('%.0f', $arr[1]) : null; //sprintf 防止超大整数变成科学计数法的问题
}

class Base62 {

	private static $base = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	private static $len = 62;

	public static function encode($data) {
		$out = '';
		for($t=floor(log10($data)/log10(self::$len)); $t>=0; $t--) {
			$bcpow = pow(self::$len, $t);
			$a = floor($data / $bcpow);
			$out .= substr(self::$base, $a, 1);
			$data = $data - ($a * $bcpow);
		}
		return $out;
	}

	public static function decode($str) {
		$out = 0;
		$len = strlen($str) - 1;
		for($t=0; $t<=$len; $t++) {
			$out += strpos(self::$base, substr($str, $t, 1)) * pow(self::$len, $len - $t);
		}
		return substr(sprintf("%f", $out), 0, -7);
	}

}

class Base256 {

	CONST BASE = 256;

	public static function encode($data) {
		$out = '';
		for ($t=floor(log10($data)/log10(self::BASE)); $t>=0; $t--) {
			$bcp = bcpow(self::BASE, $t);
			$a = floor($data / $bcp);
			$out .= chr($a);
			$data = $data - ($a * $bcp);
		}
		return $out;
	}

	public static function decode($str) {
		$out = 0;
		$len = strlen($str) - 1;
		for($t=0; $t<=$len; $t++) {
			$out += ord(substr($str, $t, 1)) * pow(self::BASE, $len - $t);
		}
		return substr(sprintf("%f", $out), 0, -7);
	}

}
