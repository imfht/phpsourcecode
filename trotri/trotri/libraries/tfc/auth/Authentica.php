<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\auth;

use tfc\ap\Ap;
use tfc\ap\ErrorException;
use tfc\saf\Cookie;
use tfc\saf\Cfg;

/**
 * Authentica class file
 * 用户身份认证类
 *
 * 配置 /cfg/app/appname/main.php：
 * <pre>
 * return array (
 *   'account' => array (
 *     'key_name' => 'auth_administrator',      // 密钥配置名
 *     'domain' => '',                          // Cookie的有效域名，缺省：当前域名
 *     'path' => '/',                           // Cookie的有效服务器路径，缺省：/
 *     'secure' => false,                       // FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输，缺省：FALSE
 *     'httponly' => true,                      // TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击，缺省：TRUE
 *     'expiry' => WEEK_IN_SECONDS,             // 记住密码时间
 *     'cookie_name' => 'atrid',                // Cookie名
 *     'cookset_password' => false,             // Cookie中设置密码，该配置不用于此类
 *     'cookset_rolenames' => true,             // Cookie中设置用户拥有的角色名，该配置不用于此类
 *     'cookset_appnames' => true,              // Cookie中设置用户拥有权限的项目名，该配置不用于此类
 *   ),
 * )
 * </pre>
 *
 * 配置 /cfg/key/cluster.php：
 * <pre>
 * return array (
 *   'auth_passport' => array (
 *     'crypt' => 'UViRN53uj7yZ5IAfdIGiq5bvRuCH9njd', // 加密密钥
 *     'sign' => 'xwFVMiM98nzW6PwW9jxCmT2mLTv5IJES',  // 签名密钥
 *     'expiry' => MONTH_IN_SECONDS,                  // 缺省的密文有效期，如果等于0，表示永久有效，单位：秒
 *     'rnd_len' => 20                                // 随机密钥长度，取值 0-32
 *   ),
 * )
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Authentica.php 1 2014-04-20 01:08:06Z huan.song $
 * @package tfc.auth
 * @since 1.0
 */
class Authentica
{
    /**
     * @var string Cookie配置名
     */
    protected $_clusterName = '';

    /**
     * @var array 寄存用户身份信息
     */
    protected $_identity = null;

    /**
     * 构造方法：初始化Cookie配置名
     * @param string $clusterName
     * @throws ErrorException 如果Cookie配置名为空，抛出异常
     */
    public function __construct($clusterName)
    {
        if (($this->_clusterName = trim($clusterName)) === '') {
            throw new ErrorException(
                'Authentica cluster name must be string and not empty.'
            );
        }
    }

    /**
     * 从Cookie中获取用户身份
     * @return array
     */
    public function getIdentity()
    {
        if ($this->_identity === null) {
            $value = $this->getCookie()->get($this->getCookieName());
            if ($value && substr_count($value, "\t") === 8) {
                list($userId, $userName, $password, $ip, $expiry, $time, $nickname, $roleNames, $extends) = explode("\t", $value);
                $this->_identity = array(
                    'user_id'    => (int) $userId,
                    'user_name'  => trim($userName),
                    'password'   => $password,
                    'ip'         => (int) $ip,
                    'expiry'     => (int) $expiry,
                    'time'       => (int) $time,
                    'nickname'   => $nickname,
                    'role_names' => explode(',', $roleNames),
                    'extends'    => $extends
                );
            }
        }

        return $this->_identity;
    }

    /**
     * 向Cookie中设置用户身份，所有参数中都不能包含"\t"字符
     * @param integer $userId
     * @param string $userName
     * @param string $password
     * @param integer $expiry
     * @param string $nickname
     * @param array $roleNames
     * @param string $extends
     * @throws ErrorException 如果用户ID错误，抛出异常
     * @throws ErrorException 如果用户名为空，抛出异常
     * @throws ErrorException 如果有效期错误，抛出异常
     * @return boolean
     */
    public function setIdentity($userId, $userName, $password, $expiry = 0, $nickname = '', array $roleNames = array(), $extends = '')
    {
        if (($userId = (int) $userId) <= 0) {
            throw new ErrorException(sprintf(
                'Authentica user id "%d" must be greater than 0.', $userId
            ));
        }

        if (($userName = trim($userName)) === '') {
            throw new ErrorException(
                'Authentica user name must be string and not empty.'
            );
        }

        if (($expiry = (int) $expiry) < 0) {
            throw new ErrorException(sprintf(
                'Authentica expiry "%d" must be greater and equal than 0.', $expiry
            ));
        }

        if ($expiry > 0) {
            $expiry += time();
        }

        $temp = array();
        foreach ($roleNames as $name) {
            if (($name = trim($name)) !== '') {
                $temp[] = $name;
            }
        }

        $roleNames = implode(',', array_unique($temp));
        $ip = ip2long(Ap::getRequest()->getClientIp());
        $value = $userId . "\t" . $userName . "\t" . $password . "\t" . $ip . "\t" . $expiry . "\t" . time() . "\t" . $nickname . "\t" . $roleNames . "\t" . $extends;

        $this->_identity = null;
        return $this->getCookie()->add($this->getCookieName(), $value, $expiry);
    }

    /**
     * 移除Cookie中的用户身份
     * @return boolean
     */
    public function clearIdentity()
    {
        $this->_identity = null;
        return $this->getCookie()->remove($this->getCookieName());
    }

    /**
     * Cookie中是否存在用户身份
     * @return boolean
     */
    public function hasIdentity()
    {
         return ($this->getIdentity() !== null ? true : false);
    }

    /**
     * 获取Cookie管理类
     * @return \tfc\saf\Cookie
     */
    public function getCookie()
    {
        static $cookie = null;

        if ($cookie === null) {
            $cookie = new Cookie($this->getClusterName());
        }

        return $cookie;
    }

    /**
     * 获取Cookie名
     * @return string
     */
    public function getCookieName()
    {
        static $cookieName = null;

        if ($cookieName === null) {
            $cookieName = Cfg::getApp('cookie_name', $this->getClusterName());
        }

        return $cookieName;
    }

    /**
     * 获取Cookie配置名
     * @return string
     */
    public function getClusterName()
    {
        return $this->_clusterName;
    }
}
