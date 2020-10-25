<?php

return array(
    //模块名
    'name' => 'Scoreshop',
    //别名
    'alias' => '积分商城',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '积分商城模块，用户可以使用积分换购商品',
    //开发者
    'developer' => '北京火木科技有限公司',
    //开发者网站
    'website' => 'http://www.hoomuu.cn',
    //前台入口，可用U函数
    'entry' => 'Scoreshop/index/index',
    //后台入口，可用U函数
    'admin_entry' => 'Admin/Scoreshop/verify',

    'icon' => 'gift',

    'can_uninstall' => 1
);