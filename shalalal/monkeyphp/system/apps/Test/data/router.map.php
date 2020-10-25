<?php

/******** 路由器到控制器的映射表——简称路由映射表（示例） ********/
//其中get可以省略，其它如post则不能省略，下同
return array(
    //静态路由
    'get/' => 'Index:index', //或 '/'=>'Index:index',
    'get/hello' => 'Index:hello', //或 '/hello'=>'Index:hello',

    'get/blog' => 'Blog\Blog:index', //或 '/blog'=>'Blog\Blog:index',

    //动态标识路由
    'get/{zh|en}:language' => 'Index:index',
    'get/blog/{s}:title' => 'Blog:get',
    'post/article/{year}/{month}/{s}:year:month:title' => 'Article:modify',
    //动态正则路由
    'get/article/([1-9]\d{3})/(1[0,1,2]|[1-9])/([^\/]+):year:month:title' => 'Article:get',
);