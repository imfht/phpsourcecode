<?php

return array(
    //模块名
    'name' => 'About',
    //别名
    'alias' => '关于我们',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '关于我们模块，可以用于展示公司介绍等',
    //开发者
    'developer' => '北京火木科技有限公司',
    //开发者网站
    'website' => 'http://www.hoomuu.cn',
    //前台入口，可用U函数
    'entry' => 'About/Index/index',

    'admin_entry' => 'Admin/About/index',

    'icon' => 'file-text',

    'can_uninstall' => 1

);