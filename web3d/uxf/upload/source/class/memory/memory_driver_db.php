<?php

/**
 * 用户数据缓存,Discuz一直没有用到
 * 
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: cache_sql.php 6757 2010-03-25 09:01:29Z cnteacher $
 */
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class Memory_Driver_Db {
    private $_conf = null;
    public  $enable;
    private $obj;
    
    function init($config) {
        $this->obj = C::t('common_cache');
        $this->enable = true;
    }

    static function &instance() {
        static $object;
        if (empty($object)) {
            $object = new self();
            $object->init(getglobal('config/memory/db'));
        }
        return $object;
    }

    function get($key) {
	if (is_array($key)) {
            return $this->getMulti($key);
        }
        static $data = null;
        if (!isset($data[$key])) {
            $cache = $this->obj->fetch($key);
            if (!$cache) {
                return false;
            }
            $data[$key] = unserialize($cache['cachevalue']);
            if (!empty($data[$key]['life']) && ($cache['dateline'] < time() - $data[$key]['life'])) {
                return false;
            }
        }
        return $data[$key]['data'];
    }
    
    /**
     * 看来必须实现
     * @param array $keys
     * @return array
     */
    function getMulti($keys) {
        $data = array();
        $caches = $this->obj->fetch_all($keys);
        foreach ($caches as $key => $cache) {
            if (!$cache) {
                continue;
            }
            $cache['cachevalue'] = unserialize($cache['cachevalue']);
            if (!empty($cache['cachevalue']['life']) && ($cache['dateline'] < time() - $cache['cachevalue']['life'])) {
                continue;
            }
            
            $data[$key] = $cache['cachevalue']['data'];
        }

        return $data;
    }
    
    function set($key, $value, $life = 3600) {
        $data = array(
            'cachekey' => $key,
            'cachevalue' => serialize(array('data' => $value, 'life' => $life)),
            'dateline' => time(),
        );

        if($cache = $this->obj->fetch($key)){
            $data[$key] = unserialize($cache['cachevalue']);
            if (empty($cache['life']) || ($cache['dateline'] > time() - $data[$key]['life'])) {
                return true;
            }
        }
        return $this->obj->insert($data, false, true);
    }

    function rm($key) {
        return $this->obj->delete($key);
    }
    
    public function clear() {
        return $this->obj->truncate();
    }

    public function inc($key, $step = 1) {
        return true;
    }

    public function dec($key, $step = 1) {
        return true;
    }

}
