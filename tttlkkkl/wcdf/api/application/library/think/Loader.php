<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use think\exception\ClassNotFoundException;

class Loader
{
    protected static $instance = [];
    // 类名映射
    protected static $map = [];

    // 命名空间别名
    protected static $namespaceAlias = [];

    /**
     * 实例化验证类 格式：[模块名/]验证器名
     * @param string $name         资源地址
     * @param string $layer        验证层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     * @param string $common       公共模块名
     * @return Object|false
     * @throws ClassNotFoundException
     */
    public static function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: Config::get('default_validate');
        if (empty($name)) {
            return new Validate;
        }

        if (isset(self::$instance[$name . $layer])) {
            return self::$instance[$name . $layer];
        }
        if (strpos($name, '/')) {
            list($module, $name) = explode('/', $name);
        } else {
            $module = Request::instance()->module();
        }
        $class = self::parseClass($module, $layer, $name, $appendSuffix);
        if (class_exists($class)) {
            $validate = new $class;
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
            if (class_exists($class)) {
                $validate = new $class;
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }
        self::$instance[$name . $layer] = $validate;
        return $validate;
    }





    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string  $name 字符串
     * @param integer $type 转换类型
     * @return string
     */
    public static function parseName($name, $type = 0)
    {
        if ($type) {
            return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }

    /**
     * 解析应用类的类名
     * @param string $module 模块名
     * @param string $layer  层名 controller model ...
     * @param string $name   类名
     * @param bool   $appendSuffix
     * @return string
     */
    public static function parseClass($module, $layer, $name, $appendSuffix = false)
    {
        $name  = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = self::parseName(array_pop($array), 1) . (App::$suffix || $appendSuffix ? ucfirst($layer) : '');
        $path  = $array ? implode('\\', $array) . '\\' : '';
        return App::$namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $path . $class;
    }

    /**
     * 初始化类的实例
     * @return void
     */
    public static function clearInstance()
    {
        self::$instance = [];
    }
}