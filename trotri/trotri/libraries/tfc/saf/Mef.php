<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\saf;

use tfc\util\Mcrypt;

/**
 * Mef class file
 * 可逆的加密算法管理类，Mcrypt Encryption Functions
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Mef.php 1 2014-04-20 01:08:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class Mef
{
    /**
     * @var instance of tfc\util\Mcrypt
     */
    protected $_mcrypt = null;

    /**
     * @var instance of tfc\saf\Keys
     */
    protected $_keys = null;

    /**
     * @var string 寄存密钥配置名
     */
    protected $_keyName = null;

    /**
     * @var instances of tfc\saf\Mef
     */
    protected static $_instances = array();

    /**
     * 构造方法：初始化密钥配置名
     * @param string $keyName
     */
    protected function __construct($keyName)
    {
        $this->_keyName = $keyName;
    }

    /**
     * 魔术方法：禁止被克隆
     */
    private function __clone()
    {
    }

    /**
     * 单例模式：获取本类的实例化对象
     * @param string $keyName
     * @return \tfc\saf\Mef
     */
    public static function getInstance($keyName)
    {
        $keyName = strtolower($keyName);
        if (!isset(self::$_instances[$keyName])) {
            self::$_instances[$keyName] = new self($keyName);
        }

        return self::$_instances[$keyName];
    }

    /**
     * 解密运算
     * @param string $ciphertext
     * @return string
     */
    public function decode($ciphertext)
    {
        return $this->getMcrypt()->decode($ciphertext);
    }

    /**
     * 加密运算
     * @param string $plaintext
     * @param integer $expiry
     * @return string
     */
    public function encode($plaintext, $expiry = null)
    {
        if ($expiry === null) {
            $expiry = $this->getKeys()->getExpiry();
        }

        return $this->getMcrypt()->encode($plaintext, $expiry);
    }

    /**
     * 获取加密算法类
     * @return \tfc\util\Mcrypt
     */
    public function getMcrypt()
    {
        if ($this->_mcrypt === null) {
            $keys = $this->getKeys();
            $this->_mcrypt = new Mcrypt($keys->getCrypt(), $keys->getSign(), $keys->getRndLen());
        }

        return $this->_mcrypt;
    }

    /**
     * 获取密钥管理类
     * @return \tfc\saf\Keys
     */
    public function getKeys()
    {
        if ($this->_keys === null) {
            $this->_keys = new Keys($this->getKeyName());
        }

        return $this->_keys;
    }

    /**
     * 获取密钥配置名
     * @return string
     */
    public function getKeyName()
    {
        return $this->_keyName;
    }
}
