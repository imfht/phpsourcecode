<?php

return [

    // 视图输出字符串内容替换,留空则会自动进行计算
    'view_replace_str'       => [
    	'__COMMON__'    => STATIC_URL . '/common',
        '__LIB__'       => STATIC_URL . '/common/lib',
        '__ZUI__'       => STATIC_URL . '/common/lib/zui',
        '__JS__'    	=> STATIC_URL . '/devtool/js',
        '__IMG__'       => STATIC_URL . '/devtool/images',
        '__CSS__'       => STATIC_URL . '/devtool/css',   
    ],
];