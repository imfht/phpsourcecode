<?php

/**
 * Config
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Config
{
    private static $configs = array();

    public function __construct()
    {
    }

    public static function get($file, $key = '', $default = '', $reload = false)
    {
        if (!defined('CONFIG_PATH')) {
            throw new \InvalidArgumentException("CONFIG_PATH is undefined!");
        }

        if ($reload || !isset(self::$configs[$file])) {
            $path = CONFIG_PATH . $file . '.php';
            if (file_exists($path)) {
                self::$configs[$file] = include_once $path;
            }
        }

        if (empty($key)) {
            return self::$configs[$file];
        } elseif (isset(self::$configs[$file][$key])) {
            return self::$configs[$file][$key];
        } elseif (strpos($key, ',')) {
            $keyArray = explode(',', $key);
            $config = self::$configs[$file];
            foreach ($keyArray as $k) {
                if (isset($config[$k])) {
                    if ($k == end($keyArray)) {
                        return $config[$k];
                    }
                    $config = $config[$k];
                } else {
                    return $default;
                }
            }
        } else {
            return $default;
        }
    }
}