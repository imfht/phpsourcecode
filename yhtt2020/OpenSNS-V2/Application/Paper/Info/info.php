<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-28
 * Time: 下午01:12
 * @author 郑钟良<zzl@ourstu.com>
 */


return array(
    //模块名
    'name' => 'Paper',
    //别名
    'alias' => '文章',
    //版本号
    'version' => '2.1.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '文章模块，可以用于展示网站介绍等',
    //开发者
    'developer' => '嘉兴想天信息科技有限公司',
    //开发者网站
    'website' => 'http://www.ourstu.com',
    //前台入口，可用U函数
    'entry' => 'Paper/Index/index',

    'admin_entry' => 'Admin/Paper/index',

    'icon' => 'file-text',

    'can_uninstall' => 1

);