<?php

/**
 * @author: ryan<zer0131@vip.qq.com>
 * @desc: 读取配置文件
 */

namespace onefox;

class Config {

    private static $_config = [];

    /**
     * 获取配置文件,支持二级点操作
     * 操作示例：Config::get('config.test')
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public static function get($name, $default = null) {
        if (!$name) {
            return null;
        }
        $conf = explode('.', $name, 2);
        $confFile = $conf[0];
        if (!isset(self::$_config[$confFile])) {
            self::$_config[$confFile] = self::_loadConfig($confFile);
        }
        if (isset($conf[1])) {
            return (is_array(self::$_config[$confFile]) && isset(self::$_config[$confFile][$conf[1]])) ? self::$_config[$confFile][$conf[1]] : $default;
        }
        return isset(self::$_config[$confFile]) ? self::$_config[$confFile] : $default;
    }

    /**
     * 设置配置项
     * @param string $name
     * @param mixed $value
     */
    public static function set($name, $value = null) {
        self::$_config[$name] = $value;
    }

    private static function _loadConfig($confFile) {
        $confFile = C::filterChars($confFile);
        $file = CONF_PATH . DS . $confFile . '.php';
        $res = C::loadFile($file);
        return $res;
    }
}

