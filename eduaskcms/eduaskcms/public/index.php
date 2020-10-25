<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

namespace think;

// 定义应用目录
define('DS', DIRECTORY_SEPARATOR);

##应用所在目录
define('APP_PATH', dirname(dirname(__FILE__)) . DS .'app' . DS);
##应用可访问根目录 -- 就是入口index.php所在文件夹，默认public，可以修改

define('WWW_ROOT', dirname(__FILE__) . DS);

define('PLUGINS_PATH', dirname(dirname(__FILE__)) . DS .'plugin' . DS);

date_default_timezone_set('PRC');

// 加载基础文件
require dirname(dirname(__FILE__)) . DS . 'thinkphp' . DS . 'base.php';
// 支持事先使用静态方法设置Request对象和Config对象

//定义自己的一些文件目录
\Env::set([
    'smarty_path' => dirname(dirname(__FILE__)) . DS . 'vendor' . DS . 'smarty-3.1.27' . DS . 'libs' . DS,
    'www_root' =>  WWW_ROOT,
    'plugin_path' => PLUGINS_PATH
]);
// 执行应用并响应
Container::get('app', [APP_PATH])->run()->send();
