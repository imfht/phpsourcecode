<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: UCT <contact@uctoo.com>
// +----------------------------------------------------------------------
return array(
    //模块名
    'name' => 'Shop',
    //别名
    'alias' => '商城',
    //版本号
    'version' => '1.0.1',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '商城模块',
    //开发者
    'developer' => 'UCToo',
    //开发者网站
    'website' => '',
    //前台入口，可用U函数
    'entry' => 'Shop/index/index',

    'admin_entry' => 'shop/Shop/index',

    'icon' => 'shopping-cart',

    'can_uninstall' => 1
);