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

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------
use \think\facade\Env;

$configs= [
    // 驱动方式
    'type' => 'File',
    // 缓存保存目录
    'path' => Env::get('RUNTIME_PATH') . 'cache/',
    // 缓存前缀
    'prefix' => 'think',
    // 缓存有效期 0表示永久缓存
    'expire' => 0,
];
//动态设置
if (file_exists($file = Env::get('root_path') . 'config/yfcmf.php')) {
    $configs_yfcmf = (array) include ($file);
    $configs = array_merge($configs, $configs_yfcmf['cache']);
}
return  $configs;
