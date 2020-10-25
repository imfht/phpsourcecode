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

use tfc\ap\ErrorException;

/**
 * Cfg class file
 * 获取配置类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Cfg.php 1 2013-04-05 15:21:06Z huan.song $
 * @package tfc.saf
 * @since 1.0
 */
class Cfg
{
    /**
     * @var array 用于寄存项目的配置
     */
    protected static $_app = null;

    /**
     * @var array 用于寄存数据库的配置
     */
    protected static $_db = null;

    /**
     * @var array 用于寄存Ral的配置
     */
    protected static $_ral = null;

    /**
     * @var array 用于寄存Key的配置
     */
    protected static $_key = null;

    /**
     * @var array 用于寄存缓存的配置
     */
    protected static $_cache = null;

    /**
     * 通过配置名获取项目的配置
     * @param string $name
     * @param string $pName
     * @param string $gName
     * @throws ErrorException 如果指定的配置名不存在，抛出异常
     * @return mixed
     */
    public static function getApp($name, $pName = null, $gName  = null)
    {
        if (self::$_app === null) {
            $file = DIR_CFG_APP . DS . 'main.php';
            self::$_app = self::getCfg($file);
        }

        if ($pName === null) {
            if (isset(self::$_app[$name])) {
                return self::$_app[$name];
            }

            throw new ErrorException(sprintf(
                'Cfg no app cfg is registered for name "%s", cfg "%s".', $name, var_export(self::$_app, true)
            ));
        }

        if ($gName === null) {
            if (isset(self::$_app[$pName][$name])) {
                return self::$_app[$pName][$name];
            }

            throw new ErrorException(sprintf(
                'Cfg no app cfg is registered for name "%s.%s", cfg "%s".', $pName, $name, var_export(self::$_app, true)
            ));
        }

        if (isset(self::$_app[$gName][$pName][$name])) {
            return self::$_app[$gName][$pName][$name];
        }

        throw new ErrorException(sprintf(
            'Cfg no app cfg is registered for name "%s.%s.%s", cfg "%s".', $gName, $pName, $name, var_export(self::$_app, true)
        ));
    }

    /**
     * 通过配置名获取数据库的配置
     * @param string $name
     * @return array
     * @throws ErrorException 如果指定的配置名不存在，抛出异常
     */
    public static function getDb($name)
    {
        if (self::$_db === null) {
            $file = DIR_CFG_DB . DS . 'cluster.php';
            self::$_db = self::getCfg($file);
        }

        if (isset(self::$_db[$name])) {
            return self::$_db[$name];
        }

        throw new ErrorException(sprintf(
            'Cfg no db cfg is registered for name "%s".', $name
        ));
    }

    /**
     * 通过配置名获取Ral的配置
     * @param string $name
     * @return array
     * @throws ErrorException 如果指定的配置名不存在，抛出异常
     */
    public static function getRal($name)
    {
        if (self::$_ral === null) {
            $file = DIR_CFG_RAL . DS . 'cluster.php';
            self::$_ral = self::getCfg($file);
        }

        if (isset(self::$_ral[$name])) {
            return self::$_ral[$name];
        }

        throw new ErrorException(sprintf(
            'Cfg no ral cfg is registered for name "%s".', $name
        ));
    }

    /**
     * 通过配置名获取Key的配置
     * @param string $name
     * @return array
     * @throws ErrorException 如果指定的配置名不存在，抛出异常
     */
    public static function getKey($name)
    {
        if (self::$_key === null) {
            $file = DIR_CFG_KEY . DS . 'cluster.php';
            self::$_key = self::getCfg($file);
        }

        if (isset(self::$_key[$name])) {
            return self::$_key[$name];
        }

        throw new ErrorException(sprintf(
            'Cfg no key cfg is registered for name "%s".', $name
        ));
    }

    /**
     * 通过配置名获取缓存的配置
     * @param string $name
     * @return array
     * @throws ErrorException 如果指定的配置名不存在，抛出异常
     */
    public static function getCache($name)
    {
        if (self::$_cache === null) {
            $file = DIR_CFG_CACHE . DS . 'cluster.php';
            self::$_cache = self::getCfg($file);
        }

        if (isset(self::$_cache[$name])) {
            return self::$_cache[$name];
        }

        throw new ErrorException(sprintf(
            'Cfg no cache cfg is registered for name "%s".', $name
        ));
    }

    /**
     * 通过配置文件路径获取配置数据
     * @param string $file
     * @return array
     * @throws ErrorException 如果指定的配置文件不存在，抛出异常
     * @throws ErrorException 如果配置文件返回值不是数组，抛出异常
     */
    public static function getCfg($file)
    {
        if (!is_file($file)) {
            throw new ErrorException(sprintf(
                'Cfg file "%s" is not a valid file.', $file
            ));
        }

        $cfg = require_once $file;
        if (is_array($cfg)) {
            return $cfg;
        }

        throw new ErrorException(sprintf(
            'Cfg file "%s" must return array only, cfg "%s".', $file, $cfg
        ));
    }
}
