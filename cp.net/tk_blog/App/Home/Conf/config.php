<?php
return array(
    //开启layout布局
    'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=>'Layout/common',

    //配置模糊查询 指定参数
    'DB_LIKE_FIELDS'=>'title|content|post_keywords',

    //定义路由
    'URL_ROUTER_ON'   => true, //开启路由
    'URL_ROUTE_RULES' => array( //定义路由规则
        'a/:aid\d'     => 'Article/index',
        'sc/:cid\d'    => 'Search/index',
        'st/:tid\d'    => 'Search/index',
        'pr'           => 'Article/praise',
        'msg'          => 'Comments/index',
        'getMsg'       => 'Comments/getMsg',
    ),

    //开启伪静态
    'URL_HTML_SUFFIX'=>'html',
);