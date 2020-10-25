<?php

/**
 * Cache
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Cache
{

    public static function __callstatic($method, $parameters = array())
    {
        if (empty($parameters) or empty($parameters[0])) {
            $configs = array();
        } else {
            $configs = $parameters[0];
        }

        return Cache\Util::instance(strtolower($method), $configs);
    }

    /**
     * 检测缓存驱动是否存在
     * @param $name
     * @return bool
     */
    public static function isExistingDriver($name)
    {
        return Cache\Util::isExistingDriver($name);
    }
}