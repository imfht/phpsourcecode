<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Web;

use Exception;
use Ke\Utils\Cipher\OpenSSLCipher;

trait HttpSecurityData
{

	private $securityPrepared = false;

	private $securityVerify = false;

	private $securityPrefix = '';

	private $securityMicrosecond = 0.00;

	private $securityFields = [];

	private $securityData = [];

	protected function encrypt($content, $salt = KE_HTTP_SECURITY_SALT)
	{
	    $cipher = OpenSSLCipher::getInstance($salt);
        return $cipher->encrypt($content);
//		$content = json_encode($content);
//		$encryptContent = gzdeflate($content, 9);
//		$hash = hash('sha256', $salt);
//		$packHash = pack('H*', $hash);
//		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
//		$cipherContent = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $packHash, $encryptContent, MCRYPT_MODE_CBC, $iv);
//		$cipherHash = md5($cipherContent, true); // 增加一个密文的指纹
//		$cipherContent = $iv . $cipherContent . $cipherHash;
//		return base64_encode($cipherContent);
	}

	protected function decrypt($content, $salt = KE_HTTP_SECURITY_SALT)
	{
        $cipher = OpenSSLCipher::getInstance($salt);
        return $cipher->decrypt($content);
//		$hash = hash('sha256', $salt);
//		$packHash = pack('H*', $hash);
//		$decodeContent = base64_decode($content);
//		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//		$ivDec = substr($decodeContent, 0, $ivSize);
//		$decryptContent = substr($decodeContent, $ivSize);
//		$decryptHash = substr($decryptContent, -16); // 取出密文指纹
//		$decryptContent = substr($decryptContent, 0, -16);
//		if ($decryptHash !== md5($decryptContent, true))
//			return false;
//		$decryptContent = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $packHash, $decryptContent, MCRYPT_MODE_CBC, $ivDec);
//		$decryptContent = gzinflate($decryptContent);
//		return json_decode($decryptContent, true);
	}

	public function mkSecurityCode($prefix, array $fields = null, $namespace = null)
	{
//		if (session_status() !== PHP_SESSION_ACTIVE)
//			throw new Exception('Use http security data should start session!');
        if (empty($namespace))
            $namespace = $prefix;
        $code = $this->encrypt([microtime(true), KE_REQUEST_HOST, KE_APP_HASH, $prefix, $fields, $namespace], KE_HTTP_SECURITY_SALT);
        if ($code === false)
            throw new Exception('Generate invalid security code!');
//		$_SESSION[KE_HTTP_SECURITY_SESS_FIELD] = $code;
        return $code;
	}

	public function decryptSecurityCode($code)
	{
		return $this->decrypt($code, KE_HTTP_SECURITY_SALT);
	}

	abstract public function getSecurityWatchData();

	public function isSecurityPrepared()
	{
		return $this->securityPrepared;
	}

	public function prepareSecurity()
	{
        if ($this->securityPrepared)
            return $this;
        $this->securityVerify = false;
        $this->securityPrepared = true;
        $data = $this->getSecurityWatchData();
        $reference = $data[KE_HTTP_SECURITY_FIELD] ?? false;
        $dec = false;
//		if (empty($_SESSION[KE_HTTP_SECURITY_SESS_FIELD]))
//			return $this;
//		$data = $this->getSecurityWatchData();
//		// 取出参考值，并删除了Session的参考值
//		$reference = $_SESSION[KE_HTTP_SECURITY_SESS_FIELD];
//		unset($_SESSION[KE_HTTP_SECURITY_SESS_FIELD]);
//		if (empty($data[KE_HTTP_SECURITY_FIELD]))
//			return $this;
//		if ($reference !== $data[KE_HTTP_SECURITY_FIELD])
//			return $this;
        if (!empty($reference))
            $dec = static::decryptSecurityCode($reference);
        if (empty($dec) || !is_array($dec) || count($dec) !== 6)
            return $this;
        list($this->securityMicrosecond, $rawHost, $rawHash, $this->securityPrefix, $this->securityFields, $ns) = $dec;
        if (KE_REQUEST_HOST === $rawHost && KE_APP_HASH === $rawHash) {
            $this->securityVerify = true;
            $pickData = depth_query($data, $ns, []);
            if (!empty($pickData) && is_array($pickData)) {
                if (!empty($fields)) {
                    $keys = array_fill_keys($this->securityFields, true);
                    $pickData = array_intersect_key($pickData, $keys);
                }
            }
            $this->securityData = $pickData;
        }
        return $this;
	}

	public function getSecurityData($field = null, $default = null)
	{
        if (!$this->securityPrepared)
            $this->prepareSecurity();
        if (isset($field)) {
            if (isset($this->securityData[$field]))
                $value = $this->securityData[$field];
            else
                $value = depth_query($this->securityData, $field, $default);
            $value = trim($value);
            return $value;
        }
        return $this->securityData;
	}

	public function getSecurityPrefix()
	{
		if (!$this->securityPrepared)
			$this->prepareSecurity();
		return $this->securityPrefix;
	}

	public function getSecurityTimestamp()
	{
		if (!$this->securityPrepared)
			$this->prepareSecurity();
		return $this->securityMicrosecond;
	}
}