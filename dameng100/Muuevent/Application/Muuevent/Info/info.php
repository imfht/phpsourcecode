<?php

return array(
    //模块名
    'name' => 'Muuevent',
    //别名
    'alias' => '活动',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '发现活动、参与活动、发起活动',
    //开发者
    'developer' => '北京火木科技有限公司',
    //开发者网站
    'website' => 'http://www.muucmf.cn',
    //前台入口，可用U函数
    'entry' => 'Muuevent/index/index',

    'admin_entry' => 'Admin/Muuevent/eventList',

    'icon' => 'map-marker',

    'can_uninstall' => 1
);