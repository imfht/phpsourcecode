<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * Hook
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/7
 */
class Hook {

    /**
     * 唯一句柄
     *
     * @access private
     * @var string
     */
    private $_handle;

    /**
     * 组件
     *
     * @access private
     * @var string
     */
    private $_component;

    /**
     * 是否触发插件的信号
     *
     * @access private
     * @var boolean
     */
    private $_signal;

    /**
     * 是否只获取一个有效返回值
     *
     * @access private
     * @var boolean
     */
    private $_once = false;

    /**
     * 插件初始化
     *
     * @access public
     * @param string $handle 插件
     */
    public function __construct($handle) {
        /** 初始化变量 */
        $this->_handle = $handle;
    }

    /**
     * 插件handle比对
     *
     * @access private
     * @param array $pluginHandles
     * @param array $otherPluginHandles
     * @return array
     */
    private static function pluginHandlesDiff(array $pluginHandles, array $otherPluginHandles) {
        foreach ($otherPluginHandles as $handle) {
            while (false !== ($index = array_search($handle, $pluginHandles))) {
                unset($pluginHandles[$index]);
            }
        }

        return $pluginHandles;
    }

    /**
     * 插件初始化
     *
     * @access public
     * @param array $plugins 插件列表
     * @return void
     */
    public static function init(array $handles) {
        //$plugins['activated'] = array_key_exists('activated', $plugins) ? $plugins['activated'] : [];
        //$plugins['handles'] = array_key_exists('handles', $plugins) ? $plugins['handles'] : [];

        /** 初始化变量 */
        //self::$_plugins = $plugins;

        Pool::value('nb\Hook:handles',$handles);
    }

    /**
     * 获取实例化插件对象
     *
     * 每一个文件&类，实例化一个hook对象
     * @access public
     * @param string $handle 插件
     * @return Hook
     */
    public static function pos($handle) {
        $class = get_called_class();
        return Pool::hash($class,$handle,function () use ($class,$handle){
            return new $class($handle);
        });
    }

    /**
     * 导出当前插件handle
     *
     * @access public
     * @return array
     */
    public static function export() {
        return Pool::get(get_called_class().':handles');
    }

    /**
     * 获取插件路径和类名
     * 返回值为一个数组
     * 第一项为插件路径,第二项为类名
     *
     * @access public
     * @param string $pluginName 插件名
     * @param string $path 插件目录
     * @return array
     * @throws \Exception
     */
    public static function portal($pluginName, $path) {
        $className = ucfirst($pluginName);
        switch (true) {
            case file_exists($pluginFileName = $path  . $pluginName . '/'.$className.'.php'):  //file_exists($pluginFileName = $path . '/' . $pluginName . '/Plugin.php'):
                $className = $className;
                break;
            case file_exists($pluginFileName = $path  . $pluginName . '.php')://file_exists($pluginFileName = $path . '/' . $pluginName . '.php'):
                $className = $className;
                break;
            default:
                throw new \Exception('Missing Plugin ' . $pluginName, 404);
        }

        return [$pluginFileName, $className];
    }

    /**
     * 版本依赖性检测
     *
     * @access public
     * @param string $version 程序版本
     * @param string $versionRange 依赖的版本规则
     * @return boolean
     */
    public static function checkDependence($version, $versionRange) {
        //如果没有检测规则,直接掠过
        if (empty($versionRange)) {
            return true;
        }

        $items = array_map('trim', explode('-', $versionRange));
        if (count($items) < 2) {
            $items[1] = $items[0];
        }

        list ($minVersion, $maxVersion) = $items;

        //对*和?的支持,4个9是最大版本
        $minVersion = str_replace(['*', '?'], ['9999', '9'], $minVersion);
        $maxVersion = str_replace(['*', '?'], ['9999', '9'], $maxVersion);

        if (version_compare($version, $minVersion, '>=') && version_compare($version, $maxVersion, '<=')) {
            return true;
        }

        return false;
    }

    /**
     * 插件调用后的触发器
     *
     * @access public
     * @param boolean $signal 触发器
     * @return \nb\Hook
     */
    public function trigger(&$signal) {
        $signal = false;
        $this->_signal = &$signal;
        return $this;
    }

    /**
     * 插件调用后的触发器
     *
     * @access public
     * @param boolean $signal 触发器
     * @return \nb\Hook
     */
    public function once(&$once) {
        $once = false;
        $this->_once = &$once;
        return $this;
    }

    /**
     * 改变Hook允许修改的私有属性
     * @param $name
     * @param $value
     */
    public function change($name,$value) {
        switch ($name) {
            case 'signal':
                $this->_signal = $value;
                break;
        }
    }

    /**
     * 判断插件是否存在
     *
     * @access public
     * @param string $pluginName 插件名称
     * @return mixed
     */
    //public function exists($pluginName) {
    //    return array_search($pluginName, self::$_plugins['activated']);
    //}

    /**
     * 设置回调函数
     *
     * @access public
     * @param string $component 当前组件
     * @param mixed $value 回调函数
     * @return void
     */
    public function __set($component, $value) {

        $weight = 0;

        if (strpos($component, '_') > 0) {
            $parts = explode('_', $component, 2);

            list($component, $weight) = $parts;
            $weight = intval($weight) - 10;

        }

        $component = $this->_handle . ':' . $component;

        //if (!isset(self::$_plugins['handles'][$component])) {
        //    self::$_plugins['handles'][$component] = [];
        //}
        $handles = Pool::hash(get_called_class().':handles',$component,[]);

        //if (!isset(self::$_tmp['handles'][$component])) {
        //    self::$_tmp['handles'][$component] = [];
        //}

        foreach ($handles as $key => $val) {
            $key = floatval($key);

            if ($weight > $key) {
                break;
            }
            else if ($weight == $key) {
                $weight += 0.001;
            }
        }

        $handles[strval($weight)] = $value;

        //ksort(self::$_plugins['handles'][$component], SORT_NUMERIC);
        ksort($handles, SORT_NUMERIC);

        Pool::hset(get_called_class().':handles',$component,$handles);
    }

    /**
     * 通过魔术函数设置当前组件位置
     *
     * @access public
     * @param string $component 当前组件
     * @return Typecho_Plugin
     */
    public function __get($component) {
        $this->_component = $component;
        return $this;
    }


    /**
     * 回调处理函数
     *
     * @access public
     * @param string $component 当前组件
     * @param string $args 参数
     * @return mixed
     */
    public function __call($component, $args) {

        $component = $this->_handle . ':' . $component;

        //参数数量
        $last = count($args);

        //如果参数数量大于0,就增加参数数组一个长度,并把第一个参数赋值给它
        //即当没有插件被触发时，将第一个参数返回
        $return = $last > 0 ? $args[0] : false;

        //将HooK对象作为最后一个参数传给插件函数
        $args[$last] = $this;

        //组件是否存在
        $handles = Pool::hget(get_called_class().':handles',$component);
        if ($handles) {
            //是否触发插件的信号
            $this->_signal = true;
            foreach ($handles as $key => $callback) {
                $return = call_user_func_array($callback, $args);

                if (false === $return) {
                    // 如果返回false 则中断行为执行
                    break;
                }
                //只获取一个有效返回值
                elseif ($return && $this->_once) {
                    break;
                }
            }
        }
        return $return;
    }

}
