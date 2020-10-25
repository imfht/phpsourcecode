<?php
return [
    //模块名
    'name' => 'index',
    //别名
    'alias' => '首页',
    //版本号
    'version' => '1.0.0',
    //排序
    'sort'    => 0,
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 0,
    //模块描述
    'summary' => '系统主页，系统核心模块',
    //开发者
    'developer' => '北京火木科技有限公司',
    //开发者网站
    'website' => 'http://www.muucmf.com',
    //前台入口，可用url函数
    'entry' => 'index/Index/index',
    //后台入口
    'admin_entry' => 'index/Admin/index',
    //允许卸载
    'can_uninstall' => 0,
    //是否自定义独立后台，1是 0否
    'custom_admin' => 0,
    //字符图标，支持fa图标库
    'icon' => 'home',
];