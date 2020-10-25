<?php

function shortUrl($input) {
	$base62 = array (
		'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
		'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'0', '1', '2', '3', '4', '5','6','7','8','9'
	);

	$hex = md5($input);
	$hexLen = strlen($hex);
	$subHexLen = $hexLen / 8;
	$output = array();

	for ($i=0; $i<$subHexLen; $i++) {
		$subHex = substr($hex, $i*8, 8);
		$int = 0x3FFFFFFF & (1*('0x'.$subHex));
		$out = '';
		for ($j=0; $j<6; $j++) {
			$val = 0x0000003D & $int;
			$out .= $base62[$val];
			$int = $int >> 5;
		}
		$output[] = $out;
	}

  return $output;
}

/*
function shortUrl($input,$single=FALSE) {
	$base62 = array (
		'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
		'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
		'0', '1', '2', '3', '4', '5','6','7','8','9'
	);

	$hex = md5($input);
	$hexLen = strlen($hex);
	$subHexLen = $hexLen / 8;
	$output = array();

	for ($i=0; $i<$subHexLen; $i++) {
		$subHex = substr($hex, $i*8, 8);
		$int = 0x3FFFFFFF & (1*('0x'.$subHex));
		$out = '';
		for ($j=0; $j<6; $j++) {
			$val = 0x0000003D & $int;
			$out .= $base62[$val];
			$int = $int >> 5;
		}
		$output[] = $out;
	}

	if ($single) {
		preg_match_all('/\d+/',$hex,$nums);
		$idx = substr(array_sum($nums[0]),0,3) % 4;
		return $output[$idx];
	}

	return $output;
}
*/

function shortUrl2($input) {
	$checkSum = crc32($input);
	$x = sprintf('%u', $checkSum);
	//Base62 transform
	$str = '';
	while ($x > 0){
		$s = $x % 62;
		if ($s > 35) {
			$s = chr($s+61);
		} elseif ($s > 9 && $s <= 35) {
			$s = chr($s + 55);
		}
		$str .= $s;
		$x = floor($x / 62);
	}
	return $str;
}

/**
 * 纠正序列化字符串中字符串长度的问题
 */
function unserialized($string) {
	return unserialize(preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $string));
}

function get_uuid_from_mysql() {
	$VF =& getInstance();
	return $VF->db->query('SELECT UUID()')->row(0);
}

function  uuidSimple() {  
	$chars = md5(uniqid(mt_rand(), true));  
	$uuid = substr($chars, 0, 8).'-' ;  
	$uuid .= substr($chars, 8, 4).'-' ;  
	$uuid .= substr($chars, 12, 4).'-' ;  
	$uuid .= substr($chars, 16, 4).'-' ;  
	$uuid .= substr($chars, 20, 12);  
	return $uuid;  
}

function uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function guid() {
    $randomString = openssl_random_pseudo_bytes(16);
    $time_low = bin2hex(substr($randomString, 0, 4));
    $time_mid = bin2hex(substr($randomString, 4, 2));
    $time_hi_and_version = bin2hex(substr($randomString, 6, 2));
    $clock_seq_hi_and_reserved = bin2hex(substr($randomString, 8, 2));
    $node = bin2hex(substr($randomString, 10, 6));

    /**
     * Set the four most significant bits (bits 12 through 15) of the
     * time_hi_and_version field to the 4-bit version number from
     * Section 4.1.3.
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
    */
    $time_hi_and_version = hexdec($time_hi_and_version);
    $time_hi_and_version = $time_hi_and_version >> 4;
    $time_hi_and_version = $time_hi_and_version | 0x4000;

    /**
     * Set the two most significant bits (bits 6 and 7) of the
     * clock_seq_hi_and_reserved to zero and one, respectively.
     */
    $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
    $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

    return sprintf('%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
}
