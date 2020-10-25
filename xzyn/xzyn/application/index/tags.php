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

// 应用行为扩展定义文件
use think\facade\Hook;

return [
    // 应用初始化
    'app_init'     => [
        // 'app\\index\\behavior\\Common'
    ],
    // 应用开始
    'app_begin'    => [
        // 'app\\index\\behavior\\Common'
    ],
    // 应用结束
    'app_end'      => [

    ],
    // 日志写入
    'log_write'    => [

    ],
    // 模块初始化
    'module_init'  => [

    ],
    // 操作开始执行
    'action_begin' => [
//		'app\\index\\behavior\\Test'
    ],
    // 响应发送标
    'response_send'      => [

    ],
    // 输出结束
    'response_end'      => [
		// 'app\\index\\behavior\\Common'
    ],
    // 控制器开始标签位
    'action_begin'      => [
		// 'app\\index\\behavior\\Common'
    ],


];
