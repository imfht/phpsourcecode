<?php

return array(
    //模块名
    'name' => 'Mpbase',
    //别名
    'alias' => '公众号',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '公众号基础设置模块，用于设置公众平台基础信息',
    //开发者
    'developer' => 'UCToo',
    //开发者网站
    'website' => 'http://www.uctoo.com',
    //前台入口，可用U函数
    'entry' => 'Mpbase/index/index',

    'admin_entry' => 'mpbase/Mpbase/index',

    'icon' => 'cog',

    'can_uninstall' => 0
);