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

use tfc\ap\HttpCookie;

/**
 * Cookie class file
 * Cookie管理类，所有的Cookie都应该加密后保存
 *
 * 配置 /cfg/app/appname/main.php：
 * <pre>
 * return array (
 *   'cookie' => array (
 *      'key_name' => 'authentication', // 密钥配置名
 *      'domain' => '.trotri.com',      // Cookie的有效域名，缺省：当前域名
 *      'path' => '/',                  // Cookie的有效服务器路径，缺省：/
 *      'secure' => false,              // FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输，缺省：FALSE
 *      'httponly' => true,             // TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击，缺省：TRUE
 *   ),
 * );
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Cookie.php 1 2014-04-20 01:08:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class Cookie
{
    /**
     * @var string 默认的Cookie的有效域名
     */
    const DEFAULT_DOMAIN = '';

    /**
     * @var string 默认的Cookie的有效服务器路径
     */
    const DEFAULT_PATH = '/';

    /**
     * @var boolean 默认的Cookie传输协议，HTTP和HTTPS协议都可传输
     */
    const DEFAULT_SECURE = false;

    /**
     * @var boolean 默认的Cookie访问协议，只能通过HTTP协议访问
     */
    const DEFAULT_HTTPONLY = true;

    /**
     * @var boolean 是否加密存取
     */
    protected $_encodeValue = true;

    /**
     * @var string 寄存Cookie配置名
     */
    protected $_clusterName = null;

    /**
     * @var array 寄存Cookie配置信息
     */
    protected $_config = null;

    /**
     * @var instance of tfc\saf\Mef
     */
    protected $_mef = null;

    /**
     * 构造方法：初始化Cookie配置名
     * @param string $clusterName
     */
    public function __construct($clusterName)
    {
        $this->_clusterName = $clusterName;
    }

    /**
     * 通过Cookie名获取值，如果都找不到，返回默认值
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($name, $defaultValue = null)
    {
        $value = HttpCookie::get($name, $defaultValue);
        if ($this->getEncodeValue()) {
            $value = $this->getMef()->decode($value);
        }

        return $value;
    }

    /**
     * 添加Cookie，如果Cookie名已经存在，则替换老值
     * @param string $name
     * @param string $value
     * @param integer $expiry
     * @return boolean
     */
    public function add($name, $value, $expiry = 0)
    {
        if ($this->getEncodeValue()) {
            $value = $this->getMef()->encode($value);
        }

        return HttpCookie::add($name, $value, $expiry, $this->getPath(), $this->getDomain(), $this->getSecure(), $this->getHttponly());
    }

    /**
     * 移除Cookie
     * @param string $name
     * @return boolean
     */
    public function remove($name)
    {
        return HttpCookie::remove($name, $this->getPath(), $this->getDomain());
    }

    /**
     * 判断Cookie名在Cookie中是否存在
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        return HttpCookie::has($name);
    }

    /**
     * 获取可逆的加密算法管理类
     * @return \tfc\saf\Mef
     */
    public function getMef()
    {
        if ($this->_mef === null) {
            $this->_mef = Mef::getInstance($this->getKeyName());
        }

        return $this->_mef;
    }

    /**
     * 获取密钥配置名
     * @return string
     */
    public function getKeyName()
    {
        return $this->getConfig('key_name');
    }

    /**
     * 获取Cookie的有效服务器路径
     * @return string
     */
    public function getPath()
    {
        return $this->getConfig('path');
    }

    /**
     * 获取Cookie的有效域名
     * @return string
     */
    public function getDomain()
    {
        return $this->getConfig('domain');
    }

    /**
     * 获取Cookie传输协议
     * @return boolean
     */
    public function getSecure()
    {
        return $this->getConfig('secure');
    }

    /**
     * 获取Cookie访问协议
     * @return boolean
     */
    public function getHttponly()
    {
        return $this->getConfig('httponly');
    }

    /**
     * 获取Cookie配置信息
     * @param mixed $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if ($this->_config === null) {
            $config = Cfg::getApp($this->getClusterName());

            $config['key_name'] = isset($config['key_name']) ? trim($config['key_name'])     : '';
            $config['path']     = isset($config['path'])     ? trim($config['path'])         : self::DEFAULT_PATH;
            $config['domain']   = isset($config['domain'])   ? trim($config['domain'])       : self::DEFAULT_DOMAIN;
            $config['secure']   = isset($config['secure'])   ? (boolean) $config['secure']   : self::DEFAULT_SECURE;
            $config['httponly'] = isset($config['httponly']) ? (boolean) $config['httponly'] : self::DEFAULT_HTTPONLY;

            $this->_config = $config;
        }

        if ($key === null) {
            return $this->_config;
        }

        return isset($this->_config[$key]) ? $this->_config[$key] : null;
    }

    /**
     * 获取Cookie配置名
     * @return string
     */
    public function getClusterName()
    {
        return $this->_clusterName;
    }

    /**
     * 获取是否加密存取
     * @return boolean
     */
    public function getEncodeValue()
    {
        return $this->_encodeValue;
    }

    /**
     * 设置是否加密存取
     * @param boolean $encodeValue
     * @return \tfc\saf\Cookie
     */
    public function setEncodeValue($encodeValue)
    {
        $this->_encodeValue = (boolean) $encodeValue;
        return $this;
    }
}
