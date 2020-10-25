<?php

/**
 * microAOP - 简洁而强大的AOP库
 *
 * @author      Dong Nan <hidongnan@gmail.com>
 * @copyright   (c) Dong Nan http://idongnan.cn All rights reserved.
 * @link        https://github.com/dongnan/microAOP/
 * @license     MIT ( http://mit-license.org/ )
 */

namespace microAOP;

/**
 * Hook
 */
class Hook {

    /**
     * 钩子群组
     * @var array 
     */
    static private $group = [];

    /**
     * 载入钩子群组
     * @param array $group
     */
    static public function load($group) {
        if (is_array($group)) {
            foreach ($group as $name => $hooks) {
                self::bindMultiple($name, (array) $hooks);
            }
        }
    }

    /**
     * 按组名批量绑定钩子
     * @param string $name  组名
     * @param array $hooks  钩子类名或实例组成的数组
     * @throws \Exception
     */
    static public function bindMultiple($name, $hooks) {
        if (!isset(self::$group[$name])) {
            self::$group[$name] = [];
        }
        foreach ($hooks as $hook) {
            if (is_string($hook)) {
                if (class_exists($hook)) {
                    self::$group[$name][$hook] = $hook;
                } else {
                    throw new \Exception("class '{$hook}' is not found");
                }
            } elseif (is_object($hook)) {
                self::$group[$name][get_class($hook)] = $hook;
            } else {
                throw new \Exception("type of the hook '{$hook}' is incorrect");
            }
        }
    }

    /**
     * 按组名绑定钩子
     * @param string $name  组名
     * @param mixed $_      钩子类名或实例
     * @throws \Exception
     */
    static public function bind($name, $_ = null) {
        $args = func_get_args();
        array_shift($args);
        self::bindMultiple($name, $args);
    }

    /**
     * 按组名批量移除钩子
     * @param string $name  组名
     * @param array $hooks  钩子类名或实例组成的数组
     * @throws \Exception
     */
    static public function unbindMultiple($name, $hooks) {
        foreach ($hooks as $hook) {
            if (is_string($hook)) {
                unset(self::$group[$name][$hook]);
            } elseif (is_object($hook)) {
                unset(self::$group[$name][get_class($hook)]);
            } else {
                throw new \Exception("type of the hook '{$hook}' is incorrect");
            }
        }
    }

    /**
     * 按组名移除钩子
     * @param string $name  组名
     * @param mixed $_      钩子类名或实例
     * @return void
     * @throws \Exception
     */
    static public function unbind($name, $_ = null) {
        if (!isset(self::$group[$name])) {
            return;
        }
        if (empty($_)) {
            unset(self::$group[$name]);
            return;
        }
        $args = func_get_args();
        array_shift($args);
        self::unbindMultiple($name, $args);
    }

    /**
     * 执行某个钩子
     * @param string $hook 插件名称
     * @param mixed $params 传入的参数
     */
    static public function exec($hook, &$params = null) {
        if (is_string($hook)) {
            if (class_exists($hook)) {
                $hook = new $hook();
            } else {
                throw new \Exception("class '{$hook}' is not found");
            }
        }
        if ($hook instanceof HookInterface) {
            $hook->run($params);
        } else {
            throw new \Exception("hook '{$hook}' does not implement the interface '\microAOP\HookInterface'");
        }
    }

    /**
     * 根据指定组名的钩子
     * @param string $name
     * @param mixed $params
     */
    static public function listen($name, &$params = null) {
        if (isset(self::$group[$name]) && is_array(self::$group[$name])) {
            foreach (self::$group[$name] as $hook) {
                self::exec($hook, $params);
            }
        }
    }

}
