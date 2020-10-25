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

    ],
    // 应用开始
    'app_begin'    => [

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

    ],
    // 响应发送标签位
    'response_send'      => [
		'app\\api\\behavior\\BuildResponse',	//输出结果规整
    ],
    // 输出结束
    'response_end'      => [

    ],


];
