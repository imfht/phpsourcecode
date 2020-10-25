<?php

namespace VF\Library;

class Encrypt {

	/**
	 * 随机密钥长度 取值 0-32
	 * 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	 * 取值越大，密文变动规律越大，密文变化 = 16 的 $ckeyLength 次方
	 * 当此值为 0 时，则不产生随机密钥
	 */
	public $ckeyLength = 4;

	private $authKey;
	private $codeProtype;
	private $keyCode;
	private $keyDecode;
	private $result;

	public function __construct()
	{
		$VF =& getInstance();
		$this->authKey = $VF->config->get('config','auth_key');
	}

	/**
	 * 加密
	 *
	 * @param string $str
	 * @param string $key
	 * @param int $expire
	 * @return string
	 */
	public function encode($str, $key='', $expire=0)
	{
		$this->codeProtype = $str;
		$this->authCode('encode', $key, $expire);
		return $this->keyCode.str_replace('=', '', base64_encode($this->result));
	}

	/**
	 * 解密
	 *
	 * @param string $code
	 * @param string $key
	 * @param int $expire
	 * @return string
	 */
	public function decode($code,$key='',$expire=0)
	{
		$this->codeProtype = $code;
		$this->authCode('decode',$key,$expire);

		$tv = substr($this->result, 0, 10);

		if(is_numeric($tv) && ($tv == 0 || $tv > time()) && substr($this->result, 10, 16) == substr(md5(substr($this->result, 26).$this->keyDecode), 0, 16)) {
			return substr($this->result, 26);
		} else {
			return '';
		}
	}

	/**
	 * 密文中间处理过程
	 *
	 * @param string $operation
	 * @param string $key
	 * @param int $expiry
	 */
	protected function authCode($operation, $key='', $expiry=0)
	{
		$operation = strtolower($operation);

		$key = md5($key ? $key : $this->authKey);
		$keya = md5(substr($key, 0, 16));
		$this->keyDecode = $keyb = md5(substr($key, 16, 16));
		$this->keyCode = $keyCode = $this->ckeyLength ? ($operation == 'decode' ? substr($this->codeProtype, 0, $this->ckeyLength): substr(md5(microtime()), - $this->ckeyLength)) : '';

		$cryptkey = $keya.md5($keya.$keyCode);
		$key_length = strlen($cryptkey);

		$string = $operation == 'decode' ? base64_decode(substr($this->codeProtype, $this->ckeyLength)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($this->codeProtype.$keyb), 0, 16).$this->codeProtype;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		$this->result = $result;
	}

}
