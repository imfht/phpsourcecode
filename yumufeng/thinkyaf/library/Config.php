<?php

/**
 * Config 对应用配置的封装，方便读取
 * Config::get('config')
 *
 * @author NewFuture
 */
class Config
{
    private static $config = [];

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name 配置参数名（支持二级配置 . 号分割）
     * @param  string $range 作用域
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        if (!($config = &self::$config)) {
            $config = Yaf_Application::app()->getConfig()->toArray();
        }
        //如果为空，返回所有的配置
        if ($key == null) {
            return $config;
        }
        if (isset($config[$key])) {
            return $config[$key];
        }
        // 非二级配置时直接返回
        if (!strpos($key, '.')) {
            $name = strtolower($key);
            if ($config_options = self::parse_config($name)) {
                return $config_options;
            }
            $value = isset($config[$name]) ? $config[$name] : null;
            return null === $value ? $default : $value;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $key, 2);
        $name[0] = strtolower($name[0]);
        if ($config_options = self::parse_config($name[0])) {
            if (isset($config_options[$name[1]])) {
                return $config_options[$name[1]];
            }
            $config = array_merge($config, $config_options);
        }
        return isset($config[$name[0]][$name[1]]) ? $config[$name[0]][$name[1]] : $default;
    }

    /**
     * 解析读取 config目录下配置文件
     * @param $name
     * @return bool
     */
    private static function parse_config($name)
    {
        if (!isset(self::$config[$name])) {
            $config_file = APP_PATH . '/conf/' . $name . '.ini';
            if (file_exists($config_file)) {
                $yaf_config_ini = new \Yaf_Config_Ini($config_file);
                self::$config[$name] = $config_arr = $yaf_config_ini->get(APP_ENV)->toArray();
                return $config_arr;
            }
        }
        return false;
    }
}
