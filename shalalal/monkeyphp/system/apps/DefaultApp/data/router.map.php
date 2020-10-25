<?php

/******** 路由器到控制器的映射表——简称路由映射表（示例） ********/
return array(
    '/' => 'Index:index',
    '/hello' => 'Index:hello',
    '/{zh|en}:language' => 'Index:index',
);