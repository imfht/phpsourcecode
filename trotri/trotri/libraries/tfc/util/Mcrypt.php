<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\ErrorException;

/**
 * Mcrypt class file
 * 可逆的加密算法类，基于流加密算法
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Mcrypt.php 1 2014-04-17 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Mcrypt
{
    /**
     * @var integer 默认的随机密钥长度，取值 0-32
     */
    const DEFAULT_RND_KEY_LEN = 8;

    /**
     * @var integer 随机密钥长度，取值 0-32
     */
    protected $_rndKeyLen = self::DEFAULT_RND_KEY_LEN;

    /**
     * @var string 加密密钥
     */
    protected $_cryptKey = '';

    /**
     * @var string 签名密钥
     */
    protected $_signKey = '';

    /**
     * 构造方法：初始化加密密钥、签名密钥、密文有效期和随机密钥长度
     * @param string $cryptKey
     * @param string $signKey
     * @param integer $rndKeyLen
     * @throws ErrorException 如果加密密钥为空，抛出异常
     * @throws ErrorException 如果签名密钥为空，抛出异常
     */
    public function __construct($cryptKey, $signKey, $rndKeyLen = self::DEFAULT_RND_KEY_LEN)
    {
        if (($cryptKey = trim($cryptKey)) === '') {
            throw new ErrorException(
                'Mcrypt cryptKey must be string and not empty.'
            );
        }

        if (($signKey = trim($signKey)) === '') {
            throw new ErrorException(
                'Mcrypt signKey must be string and not empty.'
            );
        }

        if (($rndKeyLen = (int) $rndKeyLen) < 0) {
            $rndKeyLen = 0;
        }

        $this->_cryptKey  = md5($cryptKey);
        $this->_signKey   = md5($signKey);
        $this->_rndKeyLen = min(32, $rndKeyLen);
    }

    /**
     * 解密运算
     * @param string $ciphertext
     * @return string
     */
    public function decode($ciphertext)
    {
        $rndKeyLen = $this->getRndKeyLen();
        $rndKey    = substr($ciphertext, 0, $rndKeyLen);
        $cryptKey  = $this->getCryptKey($rndKey);
        $signKey   = $this->getSignKey($rndKey);

        $string = base64_decode(substr($ciphertext, $rndKeyLen));
        $string = $this->calc($string, $cryptKey);

        $expiry    = substr($string, 0, 10);
        $sign      = substr($string, 10, 16);
        $plaintext = substr($string, 26);

        if ($expiry > 0 && $expiry <= time()) {
            return '';
        }

        if ($sign !== $this->sign($plaintext, $signKey)) {
            return '';
        }

        return $plaintext;
    }

    /**
     * 加密运算
     * @param string $plaintext
     * @param integer $expiry
     * @return string
     */
    public function encode($plaintext, $expiry = 0)
    {
        if (($expiry = (int) $expiry) < 0) {
            $expiry = 0;
        }

        $expiry    = sprintf('%010d', $expiry > 0 ? $expiry + time() : 0);
        $rndKeyLen = $this->getRndKeyLen();
        $rndKey    = $this->getRndKey($rndKeyLen);
        $cryptKey  = $this->getCryptKey($rndKey);
        $signKey   = $this->getSignKey($rndKey);

        $string = $expiry . $this->sign($plaintext, $signKey) . $plaintext;
        $string = $this->calc($string, $cryptKey);
        return $rndKey . str_replace('=', '', base64_encode($string));
    }

    /**
     * 异或位运算
     * @param string $string “密文” 或者 由“有效期{0-10}”+“签名密钥{10-26}”+“原字符串”组成的字符串
     * @param string $cryptKey
     * @return string
     */
    public function calc($string, $cryptKey)
    {
        $ret = '';

        $strLen = strlen($string);
        $iv = $this->getIv($cryptKey);
        for ($a = $j = $i = 0; $i < $strLen; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $iv[$a]) % 256;
            $tmp = $iv[$a];
            $iv[$a] = $iv[$j];
            $iv[$j] = $tmp;
            $ret .= chr(ord($string[$i]) ^ ($iv[($iv[$a] + $iv[$j]) % 256]));
        }

        return $ret;
    }

    /**
     * 通过加密密钥，获取初始化向量IV（Initialization Vector）
     * @param string $cryptKey
     * @return array
     */
    public function getIv($cryptKey)
    {
        $ret = array();

        $keyLen = strlen($cryptKey);
        $rndKey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndKey[$i] = ord($cryptKey[$i % $keyLen]);
        }

        $ret = range(0, 255);
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $ret[$i] + $rndKey[$i]) % 256;
            $tmp = $ret[$i];
            $ret[$i] = $ret[$j];
            $ret[$j] = $tmp;
        }

        return $ret;
    }

    /**
     * 对原始明文签名，防止明文被篡改
     * @param string $plaintext 原始明文
     * @param string $signKey
     * @return string
     */
    public function sign($plaintext, $signKey)
    {
        return substr(md5($plaintext . $signKey), 0, 16);
    }

    /**
     * 获取签名密钥
     * @param string $rndKey
     * @return string
     */
    public function getSignKey($rndKey)
    {
        return md5(md5($this->_signKey . $rndKey) . substr($this->_signKey, 16));
    }

    /**
     * 获取加密密钥
     * @param string $rndKey
     * @return string
     */
    public function getCryptKey($rndKey)
    {
        return md5(substr($this->_cryptKey, 0, 16) . md5($this->_cryptKey . $rndKey));
    }

    /**
     * 获取随机密钥的长度，取值 0-32。
     * 如果该值等于0，有Reused Key Attack破解的风险
     * 如果该值过小，会造成弱IV（Initialization Vector），有暴力破解的风险
     * @return integer
     */
    public function getRndKeyLen()
    {
        return $this->_rndKeyLen;
    }

    /**
     * 获取随机密钥，令密文无规律，即使原文和密钥完全相同，加密结果也会每次不同
     * @param integer $length
     * @return string
     */
    public function getRndKey($length)
    {
        return $length > 0 ? substr($this->random(), 0, $length) : '';
    }

    /**
     * 获取随机数
     * @return string
     */
    public function random()
    {
        $string = $_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_NAME'].$_SERVER['SERVER_ADDR'].$_SERVER['SERVER_PORT'].$_SERVER['HTTP_USER_AGENT'].mt_rand().microtime();
        return md5(md5($string) . mt_rand());
    }
}
