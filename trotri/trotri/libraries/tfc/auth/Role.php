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

use tfc\ap\ErrorException;
use tfc\ap\RuntimeException;
use tfc\util\Power;

/**
 * Role class file
 * 用户角色类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Role.php 1 2014-04-20 01:08:06Z huan.song $
 * @package tfc.auth
 * @since 1.0
 */
class Role
{
    /**
     * @var integer 缺省的权限
     */
    const DENY_ALL  = Power::MODE_DENY_ALL; // 0x00

    /**
     * @var integer 权限：SELECT
     */
    const SELECT    = Power::MODE_S; // 0x01

    /**
     * @var integer 权限：INSERT
     */
    const INSERT    = Power::MODE_I; // 0x02

    /**
     * @var integer 权限：UPDATE
     */
    const UPDATE    = Power::MODE_U; // 0x04

    /**
     * @var integer 权限：DELETE
     */
    const DELETE    = Power::MODE_D; // 0x08

    /**
     * @var array 寄存所有的权限
     */
    public static $powers = array(
        self::SELECT => 'SELECT',
        self::INSERT => 'INSERT',
        self::UPDATE => 'UPDATE',
        self::DELETE => 'DELETE'
    );

    /**
     * @var array 寄存所有的资源
     */
    protected $_resources = array();

    /**
     * @var string 角色名
     */
    protected $_name = null;

    /**
     * 构造方法：初始化角色名
     * @param string $name
     * @throws ErrorException 如果角色名为空，抛出异常
     */
    public function __construct($name)
    {
        if (($name = trim($name)) === '') {
            throw new ErrorException(
                'Role name must be string and not empty.'
            );
        }

        $this->_name = $name;
        $this->loadResources();
    }

    /**
     * 判断是否允许指定的权限
     * @param string $appName
     * @param string $modName
     * @param string $ctrlName
     * @param integer $power
     * @return boolean
     */
    public function isAllowed($appName, $modName, $ctrlName, $power)
    {
        if (!$this->isValid($power)) {
            return false;
        }

        $thePower = $this->getPower($appName, $modName, $ctrlName);
        if ($thePower === self::DENY_ALL) {
            return false;
        }

        return Power::isAllow($thePower, $power);
    }

    /**
     * 判断是否禁止指定的权限
     * @param string $appName
     * @param string $modName
     * @param string $ctrlName
     * @param integer $power
     * @return boolean
     */
    public function isDenied($appName, $modName, $ctrlName, $power)
    {
        return !$this->isAllowed($appName, $modName, $ctrlName, $power);
    }

    /**
     * 允许指定的权限
     * @param string $appName
     * @param string $modName
     * @param string $ctrlName
     * @param integer $power
     * @return boolean
     */
    public function allow($appName, $modName, $ctrlName, $power)
    {
        if (!$this->isValid($power)) {
            return false;
        }

        if (($appName = trim($appName)) === ''
            || ($modName = trim($modName)) === ''
            || ($ctrlName = trim($ctrlName)) === '') {
            return false;
        }

        $thePower = $this->getPower($appName, $modName, $ctrlName);
        if ($thePower === self::DENY_ALL) {
            $this->_resources[$appName][$modName][$ctrlName] = $power;
            return true;
        }

        if (Power::isAllow($thePower, $power)) {
            return true;
        }

        $this->_resources[$appName][$modName][$ctrlName] = $thePower + $power;
        return true;
    }

    /**
     * 禁止指定的权限
     * @param string $appName
     * @param string $modName
     * @param string $ctrlName
     * @param integer $power
     * @return boolean
     */
    public function deny($appName, $modName, $ctrlName, $power)
    {
        if (!$this->isValid($power)) {
            return false;
        }

        if (($appName = trim($appName)) === ''
            || ($modName = trim($modName)) === ''
            || ($ctrlName = trim($ctrlName)) === '') {
            return false;
        }

        $thePower = $this->getPower($appName, $modName, $ctrlName);
        if ($thePower === self::DENY_ALL) {
            return true;
        }

        if (Power::isDeny($thePower, $power)) {
            return true;
        }

        $thePower -= $power;
        if ($thePower > self::DENY_ALL) {
            $this->_resources[$appName][$modName][$ctrlName] = $thePower;
        }
        else {
            unset($this->_resources[$appName][$modName][$ctrlName]);
        }

        return true;
    }

    /**
     * 获取资源的权限
     * @param string $appName
     * @param string $modName
     * @param string $ctrlName
     * @return integer
     */
    public function getPower($appName, $modName, $ctrlName)
    {
        if ($this->hasResource($appName, $modName, $ctrlName)) {
            return $this->_resources[$appName][$modName][$ctrlName];
        }

        return self::DENY_ALL;
    }

    /**
     * 判断资源是否存在
     * @param string $appName
     * @param string $modName
     * @param string $ctrlName
     * @return boolean
     */
    public function hasResource($appName, $modName, $ctrlName)
    {
        return isset($this->_resources[$appName][$modName][$ctrlName]);
    }

    /**
     * 判断权限是否有效
     * @param integer $power
     * @param boolean $throwException
     * @return boolean
     * @throws RuntimeException 如果权限无效并且需要抛出异常，抛出异常
     */
    public function isValid($power, $throwException = false)
    {
        if (isset(self::$powers[$power])) {
            return true;
        }

        if ($throwException) {
            throw new RuntimeException(sprintf(
                'Role power "%d" invalid.', $power
            ));
        }

        return false;
    }

    /**
     * 加载文件中的资源
     * @return \tfc\auth\Role
     */
    public function loadResources()
    {
        if ($this->fileExists()) {
            $this->_resources = $this->readResources();
        }

        return $this;
    }

    /**
     * 从文件中读取资源
     * @return array
     * @throws ErrorException 如果文件不存在，抛出异常
     * @throws ErrorException 如果文件中保存的不是数组，抛出异常
     */
    public function readResources()
    {
        $file = $this->getFile();
        if (!is_file($file)) {
            throw new ErrorException(sprintf(
                'Role file "%s" is not a valid file.', $file
            ));
        }

        $resources = require_once $file;
        if (is_array($resources)) {
            return $resources;
        }

        throw new ErrorException(sprintf(
            'Role file "%s" must return array only, resources "%s".', $file, $resources
        ));
    }

    /**
     * 将角色资源写进文件
     * @return \tfc\auth\Role
     * @throws ErrorException 如果将资源写进文件失败，抛出异常
     */
    public function writeResources()
    {
        $file = $this->getFile();
        $data = "<?php\nreturn " . var_export($this->getResources(), true) . ";\n";
        $ret = @file_put_contents($file, $data);
        if (!$ret) {
            throw new ErrorException(sprintf(
                'Role file "%s" cannot be wrote', $file
            ));
        }

        return $this;
    }

    /**
     * 判断缓存文件是否存在
     * @return boolean
     */
    public function fileExists()
    {
        $file = $this->getFile();
        return is_file($file);
    }

    /**
     * 获取角色资源所在的文件名
     * @return string
     */
    public function getFile()
    {
        return $this->getDirectory() . DS . $this->getName() . '.php';
    }

    /**
     * 获取角色资源所在的目录名
     * @return string
     */
    public function getDirectory()
    {
        return DIR_DATA_RUNTIME_ROLES;
    }

    /**
     * 获取所有的资源
     * @return array
     */
    public function getResources()
    {
        return $this->_resources;
    }

    /**
     * 清空所有的资源
     * @return \tfc\auth\Role
     */
    public function clearResources()
    {
        $this->_resources = array();
        return $this;
    }

    /**
     * 获取角色名
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
