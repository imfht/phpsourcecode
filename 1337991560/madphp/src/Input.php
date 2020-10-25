<?php

/**
 * Input
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\Http\Util as Util;

class Input extends Http\Request
{
    /**
     * 从 get 方法获取数据
     *
     * @access   public
     * @param    string  键
     * @param    string  默认值
     * @param    bool    是否清除xss字符
     * @return   mixed
     */
    public static function get($index = NULL, $default = '', $xssClean = FALSE)
    {
        if ($index === NULL) $default = array();
        return Util::fetch($_GET, $index, $default, $xssClean);
    }

    /**
     * 从 post 方法获取数据
     *
     * @access   public
     * @param    string  键
     * @param    string  默认值
     * @param    bool    是否清除xss字符
     * @return   mixed
     */
    public static function post($index = NULL, $default = '', $xssClean = FALSE)
    {
        if ($index === NULL) $default = array();
        return Util::fetch($_POST, $index, $default, $xssClean);
    }

    /**
     * 从所有输入数据获取数据
     *
     * @access   public
     * @param    string  键
     * @param    string  默认值
     * @param    bool    是否清除xss字符
     * @return   mixed
     */
    public static function all($index = null, $default = '', $xssClean = FALSE)
    {
        if ($index === NULL) $default = array();
        return Request::input($index, $default, $xssClean);
    }

    /**
     * 判断input数组特定键的值是否存在
     *
     * @access public
     * @param  string|array  $key
     * @return bool
     */
    public static function exists($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        $input = self::all();

        foreach ($keys as $value) {
            if (!array_key_exists($value, $input)) return false;
        }

        return true;
    }

    /**
     * 判断input数组特定键的值是否为空字符
     *
     * @access public
     * @param  string|array  $key
     * @return bool
     */
    public static function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();

        foreach ($keys as $value) {
            if (self::isEmptyString($value)) return false;
        }

        return true;
    }

    /**
     * 获取特定键的input数组
     *
     * @access public
     * @param  array  $keys
     * @return array
     */
    public static function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = [];

        $input = self::all();

        foreach ($keys as $key) {
            array_set($results, $key, array_get($input, $key, null));
        }

        return $results;
    }

    /**
     * 获取排除特定键的input数组
     *
     * @access public
     * @param  array  $keys
     * @return array
     */
    public static function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        $results = self::all();

        array_forget($results, $keys);

        return $results;
    }

    /**
     * 判断input数据键的值是否为空字符串
     *
     * @access protected
     * @param  string  $key
     * @return bool
     */
    protected static function isEmptyString($key)
    {
        $boolOrArray = is_bool(Request::input($key)) || is_array(Request::input($key));

        return !$boolOrArray && trim((string) Request::input($key)) === '';
    }
}