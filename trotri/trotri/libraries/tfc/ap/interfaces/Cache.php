<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap\interfaces;

/**
 * Cache interface file
 * 缓存处理接口
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Cache.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap.interfaces
 * @since 1.0
 */
interface Cache
{
    /**
     * 通过缓存名获取缓存值
     * @param mixed $key
     * @return mixed
     */
    public function get($key);

    /**
     * 通过多个缓存名批量获取缓存值
     * @param array $keys
     * @return array
     */
    public function mget($keys);

    /**
     * 设置缓存，如果键已经存在，替换老值
     * @param mixed $key
     * @param mixed $value
     * @param integer $expire
     * @param integer $flag
     * @return boolean
     */
    public function set($key, $value, $expire = 0, $flag = 0);

    /**
     * 添加缓存，如果键已经存在，忽略新值
     * @param mixed $key
     * @param mixed $value
     * @param integer $expire
     * @param integer $flag
     * @return boolean
     */
    public function add($key, $value, $expire = 0, $flag = 0);

    /**
     * 通过缓存名删除缓存值
     * @param mixed $key
     * @return boolean
     */
    public function delete($key);

    /**
     * 通过缓存名判断缓存值是否已经存在
     * @param mixed $key
     * @return boolean
     */
    public function has($key);

    /**
     * 使所有缓存数据立即失效
     * @return boolean
     */
    public function flush();
}
