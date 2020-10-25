<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
/**
 * 钓子
 * @category HDPHP
 * @package HDPHP
 * @subpackage core
 * @author hdxj <houdunwangxj@gmail.com>
 */
abstract class Hook
{
    //钓子
    static private $hook = array();

    /**
     * 添加钓子事件
     * @param $hook 钓子名称
     * @param $action 钓子事件
     */
    static public function add($hook, $action)
    {
        if (!isset(self::$hook[$hook])) {
            self::$hook[$hook] = array();
        }
        if (is_array($action)) {
            self::$hook[$hook] = array_merge(self::$hook[$hook], $action);
        } else {
            self::$hook[$hook][] = $action;
        }
    }

    /**
     * 获得钓子信息
     * @param string $hook 钓子名
     * @return array
     */
    static public function get($hook = '')
    {
        if (empty($hook)) {
            return self::$hook;
        } else {
            return self::$hook[$hook];
        }
    }

    /**
     * 批量导入钓子
     * @param $data 钓子数据
     * @param bool $recursive 是否递归合并
     */
    static public function import($data, $recursive = true)
    {
        if ($recursive === false) {
            self::$hook = array_merge(self::$hook, $data);
        } else {
            foreach ($data as $hook => $value) {
                if (!isset(self::$hook[$hook]))
                    self::$hook[$hook] = array();
                if (isset($value['_overflow'])) {
                    unset($value['_overflow']);
                    self::$hook[$hook] = $value;
                } else {
                    self::$hook[$hook] = array_merge(self::$hook[$hook], $value);
                }
            }
        }
    }

    /**
     * 监听钓子
     * @param $hook 钓子名
     * @param null $param 参数
     * @return bool
     */
    static public function listen($hook, &$param = null)
    {
        if (!isset(self::$hook[$hook])) return false;
        foreach (self::$hook[$hook] as $name) {
            if (false == self::exe($name, $hook, $param)) return;
        }
    }

    /**
     * 执行钓子
     * @param $name 钓子名
     * @param $action 钓子方法
     * @param null $param 参数
     * @return boolean
     */
    static public function exe($name, $action, &$param = null)
    {
        if (substr($name, -4) == 'Hook') { //钓子
            $action = 'run';
        } else { //插件
            require_cache(APP_ADDON_PATH . $name . '/' . $name . 'Addon.class.php');
            $name = $name . 'Addon';
            if (!class_exists($name, false)) return false;
        }
        $obj = new $name;
        if(method_exists($obj,$action)) $obj->$action($param);
        return true;
    }
}