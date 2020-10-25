<?php

return array(
    //模块名
    'name' => 'Issue',
    //别名
    'alias' => '专辑',
    //版本号
    'version' => '2.5.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '专辑模块，适用于精品内容展示',
    //开发者
    'developer' => '嘉兴想天信息科技有限公司',
    //开发者网站
    'website' => 'http://www.ourstu.com',
    //前台入口，可用U函数
    'entry' => 'Issue/index/index',

    'admin_entry' => 'Admin/Issue/contents',

    'icon' => 'th',

    'can_uninstall' => 1
);