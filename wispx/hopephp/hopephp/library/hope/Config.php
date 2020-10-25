<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 配置操作类 ]

namespace hope;

class Config
{
    // 系统配置
    protected static $config;

    /**
     * 初始化系统配置
     * @return array
     */
    public static function init()
    {
        $files = File::getFolder(CONF_PATH)['file'];
        foreach ($files as $item => $file) {
            if (is_file(CONF_PATH . $file)) {
                $conf = self::load(CONF_PATH . $file);
                if (0 === $item) {
                    self::$config = $conf;
                } else {
                    self::$config[basename($file, EXT)] = $conf;
                }
            }
        }
        return self::$config;
    }

    /**
     * 判断配置是否设置
     * @param $name
     * @return bool
     */
    public static function has($name)
    {
        if (!strpos($name, '.')) {
            return isset(self::$config[strtolower($name)]);
        }

        $name = explode('.', $name, 2);
        return isset(self::$config[strtolower($name[0])][$name[1]]);
    }

    /**
     * 设置配置
     * @param $name 配置项
     * @param string|array $value 配置值
     * @return integer
     */
    public static function set($name, $value = '')
    {
        return self::$config[strtolower($name)] = $value;
    }

    /**
     * 获取配置数据
     * @param string $name 配置名，支持二级配置（. 分割）
     * @return array
     */
    public static function get($name = '')
    {
        if (!empty($name)) {
            if (false !== strpos($name, '.')) {

                $data = explode('.', $name, 2);
                $data[0] = strtolower($data[0]);

                if (isset($data[1]) && $data[1]) {
                    return isset(self::$config[$data[0]][$data[1]]) ? self::$config[$data[0]][$data[1]] : null;
                }
                return isset(self::$config[$data[0]]) ? self::$config[$data[0]] : null;
            } else {
                return isset(self::$config[$name]) ? self::$config[$name] : null;
            }
        }
        return self::$config;
    }

    /**
     * 加载配置文件
     * @param $file
     * @return mixed
     */
    public static function load($file)
    {
        $file = str_replace('\\', '/', $file);
        if (is_file($file)) {
            return require $file;
        }
    }
}