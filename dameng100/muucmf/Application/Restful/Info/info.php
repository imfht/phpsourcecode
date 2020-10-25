<?php

return array(
    //模块名
    'name' => 'Restful',
    //别名
    'alias' => 'RestfulApi接口',
    //版本号
    'version' => '0.1.0',
    //是否商业模块,1是，0，否
    'is_com' => 1,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 0,
    //模块描述
    'summary' => '为移动应用、微信应用等提供RESTFUL接口服务',
    //开发者
    'developer' => '北京火木科技有限公司',
    //开发者网站
    'website' => 'http://www.hoomuu.cn',
    //前台入口，可用U函数
    'entry' => 'Restful/Index/Index',

    'admin_entry' => 'Admin/Restful/config',

    'icon' => 'random',

    'can_uninstall' => 1
);