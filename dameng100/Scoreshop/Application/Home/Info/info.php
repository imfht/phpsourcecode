<?php

return array(
    //模块名
    'name' => 'Home',
    //别名
    'alias' => '主页',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '通用首页模块，系统共用控制器放置于该模块下',
    //开发者
    'developer' => '北京火木科技有限公司',
    //开发者网站
    'website' => 'http://www.hoomuu.cn',
    //前台入口，可用U函数
    'entry' => 'Home/index/index',

    'admin_entry' => 'Admin/Home/config',

    'icon'=>'home',

    'can_uninstall' => 0

);