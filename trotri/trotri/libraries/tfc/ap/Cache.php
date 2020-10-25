<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

use tfc\ap\interfaces;

/**
 * Cache abstract class file
 * 缓存数据基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Cache.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
abstract class Cache implements interfaces\Cache
{
    /**
     * @var array 用于寄存缓存的数据
     */
    protected $_caches = array();

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::get()
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->_caches[$key];
        }

        return null;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::mget()
     */
    public function mget($keys = null)
    {
        return $this->_caches;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::set()
     */
    public function set($key, $value, $expire = 0, $flag = 0)
    {
        $this->_caches[$key] = $value;
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::add()
     */
    public function add($key, $value, $expire = 0, $flag = 0)
    {
        if (!$this->has($key)) {
            $this->_caches[$key] = $value;
        }

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::delete()
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            unset($this->_caches[$key]);
        }

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::has()
     */
    public function has($key)
    {
        return isset($this->_caches[$key]);
    }

    /**
     * (non-PHPdoc)
     * @see \tfc\ap\interfaces\Cache::flush()
     */
    public function flush()
    {
        $this->_caches = array();
        return true;
    }
}
