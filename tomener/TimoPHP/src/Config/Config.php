<?php
/*********************************************
 * TimoPHP a Fast Simple Smart PHP FrameWork *
 * Author: Tommy 863758705@qq.com            *
 * Link: http://www.TimoPHP.com/             *
 * Since: 2016                               *
 *********************************************/

namespace Timo\Config;


class Config
{
    /**
     * @var array 配置数据集
     */
    protected static $data = [];

    /**
     * @var array 运行时配置
     */
    protected static $runtime = [];

    /**
     * 获取配置项，没有返回null
     *
     * @param string $index 配置项（以.号隔开）
     * @return mixed|null
     */
    public static function get($index)
    {
        $keys = explode('.', $index);
        $name = $keys[0];
        if (!isset(self::$data[$name])) {
            static::load($name, $name);
        }

        $config = &self::$data;
        foreach ($keys as $key) {
            if (isset($config[$key])) {
                $config = &$config[$key];
            } else {
                return null;
            }
        }

        return $config;
    }

    /**
     * 获取运行时配置项
     *
     * @param string $index
     * @return mixed|null
     */
    public static function runtime(string $index = '')
    {
        return static::get('runtime.' . $index);
    }

    /**
     * 动态设置配置
     *
     * @param string $index 配置项
     * @param string|array $value 配置值
     * @return bool
     */
    public static function set($index, $value)
    {
        $keys = explode('.', $index);

        $config = &self::$data;
        $len = count($keys) - 1;
        foreach ($keys as $i => $key) {
            if (!isset($config[$key])) {
                $config[$key] = $i < $len ?  [] : $value;
            }
            $config = &$config[$key];
        }
        $config = $value;
        return true;
    }

    /**
     * 设置运行时配置
     *
     * @param $index
     * @param string $values
     * @return bool
     */
    public static function setRuntime($index, $values = '')
    {
        return static::set('runtime.' . $index, $values);
    }

    /**
     * 加载配置文件
     *
     * @param string $config_file 配置文件名或文件路径
     * @param string $name 配置名称
     * @return array|null
     * @throws \Exception
     */
    public static function load($config_file, $name = '')
    {
        if (!is_file($config_file)) {
            $env_path = !defined('ENV') ? '' : strtolower(ENV) . DS;
            $config_file = ROOT_PATH . 'config' . DS . $env_path . $config_file . '.config.php';
        }
        if (!is_file($config_file)) {
            throw new \Exception('config file ' . $config_file . ' no exist', 0);
        }

        $config = include $config_file;
        if (empty($name)) {
            return $config;
        }

        if (!isset(self::$data[$name])) {
            self::$data[$name] = $config;
            return null;
        }
        foreach ($config as $key => $value) {
            if (isset(self::$data[$name][$key]) && is_array(self::$data[$name][$key]) && is_array($value)) {
                self::$data[$name][$key] = array_merge(self::$data[$name][$key], $value);
            } else {
                self::$data[$name][$key] = $value;
            }
        }
        return null;
    }

    /**
     * 判断配置是否存在
     *
     * @param $index
     * @return bool
     */
    public static function has($index)
    {
        return null === static::get($index) ? false : true;
    }
}
