<?php
namespace app\services;

/**
 * 类名：IntConvert
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/IntConvert
 * 说明：数字字符串互换类，常见的应用场景比如邀请码
 */

class IntConvert {

	/**
	 * KeyMap 在初始时，建议重新生成一下
	 */
	static private $keyMap = [
		'LOXB3V64IGUEYCQF8A72WKSHN915RDZM',
		'DZ7O3VMWHB85CELGI6XQYSRA9N4K1FU2',
		'BZNS6WAG83IDK5M2OCUEF4RLQV917HYX',
		'LXOZQKEI7F49US5N1M36ADRV2YW8CGHB',
		'XRNOYZDGIS45UM3LV7E6QW8FC91BH2KA',
		'UQW7V4YSRD1B6KML32ZANF8E9O5GXHCI',
		'MNQ9Y3I65K4VOGE2L7DHRSXAW1UZBC8F',
		'G1QDUKIM4OX67LNRYEB8V3S9ACF5WZ2H',
		'65I13C7XM9ZFKWNR2DEUHQLYB48AGSVO',
		'53ZK4CG86LOMQRAVNEFI1XU7HS2YB9DW',
		'Q4FC3ZV8GYNRH1297DKXUBE5ALIO6WSM',
		'CSI2ON7MBYRVWH63UX8LQ9FG5DEAKZ14',
		'3D1GWSEN7L9IQ8Y5UMVXACORKBHZ42F6',
		'285V46AGWB9LI3CKM1ONURYSF7QHDXZE',
		'OX7NB2SU8IMKQ1Z9YEWVD6H53C4AFLGR',
		'W9RL6NS74HG15ZID2OCAYFBEM83UQXVK',
	];

	/**
	 * 生成随机Key
	 */
	static public function randomKey() {

		header('content-type: text/text; charset=utf-8');
		echo "	#请复制到 IntConvert 头部\n";
		echo "	static private $" . "keyMap = [\n";

		for ($i = 0; $i < 16; $i++) {
			$keys = self::$keyMap[0];
			$keys_new = '';
			$word = '';

			$len = strlen($keys);
			while ($len > 0) {
				$word = substr($keys, rand(0, $len - 1), 1);
				$keys = str_replace($word, '', $keys);
				$keys_new .= $word;
				$len = strlen($keys);
			}
			echo "		'$keys_new',\n";
		}
		echo "	];\n";
		die();
	}

	/**
	 * 将数字编码为字符串
	 */
	static public function toString($num = 0) {

		// 对传入的数字，取hash的第一位
		$hash = self::getHash($num);

		// 根据Hash，选择不同的字典
		$keymap = self::getKeyMap($hash);

		// 数字转二进制
		$map = self::fixBin(decbin($num));

		// 如果不足10位，前面自动补全对应个数的0
		$len = strlen($map);
		if ($len < 10) {
			$map = substr('00000000000000000000', 0, (10 - $len)) . $map;
		}

		// 按5个一组，编码成数字，根据KeyMap加密
		$keys = '';
		$len = strlen($map);
		for ($index = 0; $index < strlen($map); $index += 5) {
			$keys .= substr($keymap, bindec(substr($map, $index, 5)), 1);
		}

		return $hash . $keys;
	}

	/**
	 * 将字符串编码为数字
	 */
	static public function toInt($str = '') {

		//根据生成规则，最小长度为3
		if (strlen($str) < 3) {
			return false;
		}
		$hash = substr($str, 0, 1);
		$keys = substr($str, 1);

		// 根据Hash，选择不同的字典
		$keymap = self::getKeyMap($hash);

		$bin = '';
		// 根据字典，依次 index，并转换为二进制
		for ($i = 0; $i < strlen($keys); $i++) {
			for ($index = 0; $index < strlen($keymap); $index++) {
				if (strtoupper(substr($keys, $i, 1)) === substr($keymap, $index, 1)) {
					$bin .= self::fixBin(decbin($index));
				}
			}
		}

		// 二进制转换为数字
		$num = bindec($bin);

		if (self::getHash($num) === $hash) {
			return $num;
		} else {
			return false;
		}

	}

	/**
	 * 根据Hash取字典
	 */
	static private function getKeyMap($hash = 'A') {
		return self::$keyMap[hexdec($hash)];
	}

	/**
	 * 不足5位的二进制，自动补全二进制位数
	 */
	static private function fixBin($bin = '110') {
		$len = strlen($bin);
		if ($len % 5 != 0) {
			$bin = substr('00000', 0, (5 - $len % 5)) . $bin;
		}
		return $bin;
	}

	/**
	 * 对数字进行Hash
	 */
	static private function getHash($num = 0) {
		return strtoupper(substr(md5(self::getKeyMap(0) . $num), 1, 1));
	}
}
