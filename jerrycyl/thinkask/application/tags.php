<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
// 应用行为扩展定义文件
return [
    // 应用初始化
    'app_init'     => [

    'app\common\behavior\Inithook',

     
     ],
    // 应用开始
    'app_begin'    => [
    

    ],
    // 模块初始化
    'module_init'  => [   
        
        
    ],
    // 操作开始执行Initlang
    'action_begin' => [
        'app\common\behavior\InitConfig',
        'app\common\behavior\Initlang',
        'app\common\behavior\InitCache',
        'app\common\behavior\InitAuth',
        
        
     ],
    // 视图内容过滤
    'view_filter'  => [
    ],
    // 日志写入
    'log_write'    => [
    ],
    // 应用结束
    'app_end'      => [
    ],
];
