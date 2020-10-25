<?php

class CACHE
{
    /**
     * cache type
     *
     * @var string
     */
    private static $cache_type = '';
    /**
     * cache prefix
     *
     * @var string
     */
    private static $cache_pre = '';
    /**
     * cache config
     *
     * @var array
     */
    private static $cache_conf = array();
    /**
     * instance of cache
     *
     * @var array
     */
    public static $instance = array();


    /**
     * @return object instance of cache
     */
    public static function instance($cache_name = '') {
        static $default_cache = NULL;
        if ($default_cache && !$cache_name) {
            $cache_name = $default_cache;
        }
        $default_is_null = is_null($default_cache);
        if ($default_is_null || !isset(self::$instance[$cache_name])) {
            //find cache engine
            $cache_conf = &core::$conf['cache'];
            foreach ($cache_conf as $type => $conf) {
                // default cache is first cache conf key
                if ($default_is_null) {
                    $default_cache = $type;
                    !$cache_name && $cache_name = $type;
                }
                if ($cache_name != $type) {
                    continue;
                }
                $cache_enigne                = $type . '_cache';
                self::$cache_type            = $type;
                self::$cache_pre             = isset($conf['pre']) ? $conf['pre'] : '';
                self::$instance[$cache_name] = new $cache_enigne($cache_conf[$type]);
                if (self::$instance[$cache_name]->init()) {
                    return self::$instance[$cache_name];
                }
                self::$instance[$cache_name] = false;
                break;
            }
        }
        return self::$instance[$cache_name];
    }

    /**
     * @param $key
     *
     * @return string
     */
    public static function key($key) {
        if (is_array($key)) {
            foreach ($key as &$k) {
                $k = self::key($k);
            }
        } else {
            $key = self::$cache_pre . $key;
        }
        return $key;
    }

    /**
     * check cache open
     *
     * @return bool
     */
    public static function opened($cache_name = '') {
        if (!isset(self::$instance[$cache_name])) {
            $instance = self::instance($cache_name);
        } else {
            $instance = &self::$instance[$cache_name];
        }
        return $instance !== false;
    }

    /**
     * get cache by key
     *
     * @param $key
     *
     * @return mixed
     */
    public static function get($key) {
        if (DEBUG) {
            $_SERVER['cache']['get'][] = $key;
        }
        return call_user_func(array(self::instance(), 'get'), self::key($key));
    }

    /**
     * set cache
     *
     * @param     $key
     * @param     $val
     * @param int $expire
     *
     * @return mixed
     */
    public static function set($key, $val, $expire = 0) {
        if (DEBUG) {
            $_SERVER['cache']['set'][] = func_get_args();
        }
        return call_user_func(array(self::instance(), 'set'), self::key($key), $val, $expire);
    }

    /**
     * update cache
     *
     * @param $key
     * @param $val
     * @param $expire
     *
     * @return mixed
     */
    public static function update($key, $val, $expire) {
        if (DEBUG) {
            $_SERVER['cache']['update'][] = func_get_args();
        }
        return call_user_func(array(self::instance(), 'update'), self::key($key), $val, $expire);
    }

    /**
     * update cache
     *
     * @param $key
     *
     * @return mixed
     */
    public static function delete($key) {
        if (DEBUG) {
            $_SERVER['cache']['delete'][] = func_get_args();
        }
        return call_user_func(array(self::instance(), 'delete'), self::key($key));
    }

    /**
     * truncate cache
     *
     * @param string $pre
     *
     * @return mixed
     */
    public static function truncate($pre = '') {
        if (DEBUG) {
            $_SERVER['cache']['truncate'][] = func_get_args();
        }
        return call_user_func(array(self::instance(), 'truncate'), $pre);
    }

    /**
     *  lock by cache provider
     *
     * @param     $key
     * @param int $expire         expire time form lock
     * @param int $max_lock_count max lock count
     * @param int $lock_step_time lock step time
     *
     * @return bool
     */
    public static function lock($key, $expire = 10000, $max_lock_count = 1000, $lock_step_time = 5000) {
        $key         = '_lock_' . $key;
        $sleep_count = 0;
        !$lock_step_time && $lock_step_time = 5000;

        if (self::get($key)) {
            while (true) {
                usleep($lock_step_time);
                // until lock
                if (!self::get($key)) {
                    break;
                }
                $sleep_count++;
                if ($max_lock_count && $max_lock_count <= $sleep_count) {
                    return false;
                }
            }
        }
        self::set($key, 1, $expire);
        return true;
    }

    /**
     * unlock by cache
     *
     * @param $key
     */
    public static function unlock($key) {
        $key = '_lock_' . $key;
        return self::delete($key);
    }
}

?>