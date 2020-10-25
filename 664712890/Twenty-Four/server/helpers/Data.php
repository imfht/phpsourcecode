<?php

namespace app\helpers;

class Data{
	/**
	 * 解码
	 */
	public static function decode($str){
		$mask = [];
		$data = '';
		$msg = unpack('H*', $str);
		$s = $n = 0;

		$head = substr($msg[1], 0, 2);
		if($head == '81') {
			$pos = ['fe'=>4, 'ff'=>16];
			$pre = substr($msg[1], 2, 2);
			isset($pos[$pre]) && $msg[1] = substr($msg[1], $pos[$pre]);

			for($i = 4; $i < 12; $i += 2) {
				$mask[] = hexdec( substr($msg[1], $i, 2) );
			}

			$s = 12;
		}

		$len = strlen($msg[1]) - 2;
		for($i = $s; $i <= $len; $i += 2) {
			$data .= chr( $mask[$n%4] ^ hexdec(substr($msg[1], $i, 2)) );
			$n++;
		}

		return $data;
	}


	/**
	 * 编码
	 */
	public static function encode($str) {
		$frame = ['81'];

		$len = strlen($str);
		$hex = dechex($len);
		
		if($len < 126) {
			$frame[1] = sprintf("%02s", $hex);
		} else if($len < 65025) {
			$frame[1] = '7e'.str_repeat('0', 4 - strlen($hex)) . $hex;
		} else {
			$frame[1] = '7f'.str_repeat('0', 16 - strlen($hex)) . $hex;
		}
		
		$frame[2] = self::ord_hex($str);
		$data = implode('', $frame);
		return pack("H*", $data);
	}

	public static function ord_hex($str)  {
		$res = '';
		$l = strlen($str);
		for ($i= 0; $i < $l; $i++) {
			$res .= dechex(ord($str{$i}));
		}
		return $res;
	}
}