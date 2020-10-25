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

namespace hope;

class App
{
    /**
     * 应用Debug
     * @var
     */
    public static $debug;

    /**
     * 全局配置
     * @var
     */
    public static $config;

    /**
     * 运行应用
     * @return array
     * @throws \Exception
     */
    public static function run()
    {
        try {

            self::init();

            // 设置系统时区
            date_default_timezone_set(self::$config['default_timezone']);

            // 是否开启debug
            self::$debug = self::$config['app_debug'];

            // 初始化路由
            Route::init();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return self::$config;
    }

    /**
     * 初始化应用
     * @param string $module
     */
    public static function init($module = '')
    {
        // 定位模块目录
        $module = $module ? $module . DS : '';

        // 加载第三方拓展包
        require_once VENDOR_PATH . 'autoload' . EXT;

        // 加载助手函数
        include HOPE_PATH . 'helper' . EXT;

        // 初始化系统配置
        self::$config = Config::init();

        $path = APP_PATH . $module;

        // 加载公共文件
        if (is_file($path . 'common' . EXT)) {
            require $path . 'common' . EXT;
        }

        // 如果有自定义配置则加载
        if (is_file($path . 'config' . EXT)) {
            require $path . 'config' . EXT;
        }

    }
}