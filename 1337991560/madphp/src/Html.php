<?php

/**
 * Html
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\Html\Helper;

class Html
{
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(new Helper, $method), $parameters);
    }
    
    /**
     * 图片别名加载
     * @param  dynamic  mixed  配置文件中的别名
     * @return string
     */
    public static function img()
    {
        $configKey = func_get_arg(0);
        list($file, $key) = explode('.', $configKey);
        $args = func_get_args();
        array_shift($args);
        $imgAliases = Config::get($file, $key);
        $imgArray = array_map(function ($aliases) use ($imgAliases) {
            if (isset($imgAliases[$aliases])) {
                return self::image($imgAliases[$aliases]);
            }
        }, $args);
        return implode('', array_filter($imgArray));
    }

    /**
     * 样式别名加载
     * @param  dynamic  mixed  配置文件中的别名
     * @return string
     */
    public static function css()
    {
        $configKey = func_get_arg(0);
        list($file, $key) = explode('.', $configKey);
        $args = func_get_args();
        array_shift($args);
        $cssAliases = Config::get($file, $key);
        $styleArray = array_map(function ($aliases) use ($cssAliases) {
            if (isset($cssAliases[$aliases])) {
                return self::style($cssAliases[$aliases]);
            }
        }, $args);
        return implode('', array_filter($styleArray));
    }

    /**
     * 脚本别名加载
     * @param  dynamic  mixed  配置文件中的别名
     * @return string
     */
    public static function js()
    {
        $configKey = func_get_arg(0);
        list($file, $key) = explode('.', $configKey);
        $args = func_get_args();
        array_shift($args);
        $jsAliases = Config::get($file, $key);
        $scriptArray = array_map(function ($aliases) use ($jsAliases) {
            if (isset($jsAliases[$aliases])) {
                return self::script($jsAliases[$aliases]);
            }
        }, $args);
        return implode('', array_filter($scriptArray));
    }
}

