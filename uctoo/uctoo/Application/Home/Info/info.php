<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

return array(
    //模块名
    'name' => 'Home',
    //别名
    'alias' => '网站主页',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '首页模块，主要用于展示网站内容',
    //开发者
    'developer' => '深圳优创智投科技有限公司',
    //开发者网站
    'website' => 'http://www.uctoo.com',
    //前台入口，可用U函数
    'entry' => 'Home/index/index',

    'admin_entry' => 'home/Home/config',

    'icon'=>'home',

    'can_uninstall' => 0

);