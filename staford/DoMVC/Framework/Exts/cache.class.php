<?php

/**
 * 缓存类
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Cache
{
    private static $arr_block_tags = array(); //块缓存标记
    private static $str_cache_dir = ''; //缓存目录
    private static $str_cache_full_dir;
    private static $str_cache_block_dir;
    private static $str_cache_data_dir;

    /**
     * 设置缓存文件目录
     */
    public static function setCacheDir($dir)
    {
        if (!empty($dir)) {
            $path = RTM_DIR . $dir . DS;
            if (!is_dir($path))
                @mkdir($path, 0777, true);
            self::$str_cache_dir = $path;
            self::$str_cache_full_dir = self::$str_cache_dir . 'full' . DS;
            self::$str_cache_block_dir = self::$str_cache_dir . 'block' . DS;
            self::$str_cache_data_dir = self::$str_cache_dir . 'data' . DS;
        }
        if (!is_dir(self::$str_cache_full_dir)) {
            @mkdir(self::$str_cache_full_dir, 0777, true);
        }
        if (!is_dir(self::$str_cache_block_dir)) {
            @mkdir(self::$str_cache_block_dir, 0777, true);
        }
        if (!is_dir(self::$str_cache_data_dir)) {
            @mkdir(self::$str_cache_data_dir, 0777, true);
        }
        if (ob_get_level() == 0)
            ob_start();
    }

    /**
     * 页面缓存
     */
    public static function full($expire = 1800)
    {
        if (ob_get_level() > 0 && ob_get_length() > 0) {
            $data = ob_get_contents();
            $key = md5($_GET['c'] . $_GET['a']) . '.php';
            $data = array('expire' => $expire, 'data' => serialize(htmlentities($data)));
            $data = serialize($data);
            if (file_put_contents(self::$str_cache_full_dir . $key, $data, LOCK_EX)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 局部缓存开始
     */
    public static function block($key, $expire = 1800)
    {
        if (isset(self::$arr_block_tags[$key]))
            return false;
        self::$arr_block_tags[$key] = $expire;
        ob_start();
    }

    /**
     * 局部缓存结束
     */
    public static function catchBlock($key)
    {
        if (ob_get_level() < 1 || ob_get_length() < 1)
            return false;
        if (!isset(self::$arr_block_tags[$key]))
            return false;
        $file = md5($key) . '.php';
        $data = ob_get_contents();
        $data = serialize(htmlentities($data));
        $data = array('expire' => self::$arr_block_tags[$key], 'data' => $data);
        $data = serialize($data);
        unset(self::$arr_block_tags[$key]);
        if (file_put_contents(self::$str_cache_block_dir . $file, $data, LOCK_EX)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 数据缓存
     */
    public static function data($key, $value, $expire = 60)
    {
        $data = array('expire' => $expire, 'data' => serialize($value));
        $data = serialize($data);
        $key = md5($key) . '.php';
        if (file_put_contents(self::$str_cache_data_dir . $key, $data, LOCK_EX)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 读取页面缓存
     */
    public static function getFull()
    {
        clearstatcache();
        $key = self::$str_cache_full_dir . md5($_GET['c'] . $_GET['a']) . '.php';
        if (!is_file(self::$str_cache_full_dir . $key))
            return false;
        $data = unserialize(file_get_contents($key));
        if ($data['expire'] == 0)
            return html_entity_decode(unserialize($data['data']));
        if (filemtime($key) + $data['expire'] < time())
            return false;
        return html_entity_decode(unserialize($data['data']));
    }

    /**
     * 读取局部数据
     */
    public static function getBlock($key)
    {
        clearstatcache();
        $key = self::$str_cache_block_dir . md5($key) . '.php';
        if (!is_file($key))
            return false;
        $data = unserialize(file_get_contents($key));
        if ($data['expire'] == 0)
            return html_entity_decode(unserialize($data['data']));
        if (filemtime($key) + $data['expire'] < time())
            return false;
        return html_entity_decode(unserialize($data['data']));
    }

    /**
     * 读取数据缓存
     */
    public static function getData($key)
    {
        clearstatcache();
        $file = self::$str_cache_data_dir . md5($key) . '.php';
        if (!is_file($file))
            return false;
        $data = unserialize(file_get_contents($file, false));
        if ($data['expire'] == 0)
            return unserialize($data['data']);
        if (filemtime($file) + $data['expire'] < time())
            return false;
        return unserialize($data['data']);
    }
}

?>