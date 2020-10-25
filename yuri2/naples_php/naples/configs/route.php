<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/6
 * Time: 10:48
 */

//定义路由相关配置

return [
    'alias'=>[
        //别名->标准
        '/SysNaples/(.*?)$'=>function($s0,$s1){
            return '/SysNaples/'.$s1;
        },
    ],
    'reverse'=>[
        //标准->别名
        'Blog/Article/read/id/(\d+)'=>function($matches){
            return 'Blog/'.$matches[1];
        }
    ]

];