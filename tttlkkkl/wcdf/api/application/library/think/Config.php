<?php
/**
 * 重写thinkPHP配置获取方法
 */

namespace think;

class Config
{
    // 参数作用域
    /**
     * 获取配置参数 为空则获取所有配置
     *
     * @param string $name  配置参数名（支持二级配置 .号分割）
     * @param string $range 作用域
     *
     * @return mixed
     */
    public static function get($name = null, $range = '')
    {
        if (!$name) {
            return null;
        }
        $nameArr = explode('.', $name);
        $config = \Yaf\Registry::get('config')->toArray();
        if ($nameArr) {
            if (count($nameArr) == 2) {
                return isset($config[$nameArr[0]][$nameArr[1]]) ? $config[$nameArr[0]][$nameArr[1]] : null;
            }
            if (count($nameArr) == 1) {
                return isset($config[$nameArr[0]]) ? $config[$nameArr[0]] : null;
            }
        }
        return isset($config[$name]) ? $config[$name] : null;
    }
}
