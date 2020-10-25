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

// [ 框架引导文件 ]

namespace hope;

// 常量配置

defined('HOPE') or define('HOPE', 'HOPE');

// 框架版本
defined('HOPE_VERSION') or define('HOPE_VERSION', '1.0');

// 记录当前Unix时间戳
defined('HOPE_START_TIME') or define('HOPE_START_TIME', microtime(true));

// 记录内存消耗
defined('HOPE_START_MEM') or define('HOPE_START_MEM', memory_get_usage());

// 文件后缀
defined('EXT') or define('EXT', '.php');

// 当前系统分隔符
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// 当前访问路径
defined('THIS_PATH') or define('THIS_PATH', __DIR__ . DS);

// 框架核心文件目录
defined('HOPE_PATH') or define('HOPE_PATH', ROOT_PATH . 'hopephp' . DS);

// 应用目录
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . 'app' . DS);

// 核心文件
defined('LIB_PATH') or define('LIB_PATH', HOPE_PATH . 'library' . DS);

// 缓存目录
defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);

// 应用缓存
defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);

// 日志
defined('LOH_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);

// 配置目录
defined('CONF_PATH') or define('CONF_PATH', ROOT_PATH . 'config' . DS);

// 第三方拓展目录
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

// 载入Loader类
require LIB_PATH . 'hope/Loader.php';

// 注册自动加载
\hope\Loader::register();

// 注册错误和异常处理类
\hope\Error::register();

// 执行应用
\hope\App::run();