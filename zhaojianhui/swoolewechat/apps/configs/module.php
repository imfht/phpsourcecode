<?php
//默认访问模块、控制器、方法
$module['default'] = ['directory' => '', 'controller' => 'Index', 'view' => 'index'];
//模块列表,大写开头，后面都是小写
$module['list'] = ['Api', 'Wechat', 'Admin', 'Index', 'User'];

return $module;
