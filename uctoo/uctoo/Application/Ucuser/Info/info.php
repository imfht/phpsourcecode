<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

return array(
    //模块名
    'name' => 'Ucuser',
    //别名
    'alias' => '微会员',
    //版本号
    'version' => '1.0.0',
    //是否商业模块,1是，0，否
    'is_com' => 0,
    //是否显示在导航栏内？  1是，0否
    'show_nav' => 1,
    //模块描述
    'summary' => '微信用户中心模块，系统核心模块',
    //开发者
    'developer' => 'UCToo',
    //开发者网站
    'website' => 'http://www.uctoo.com',
    //前台入口，可用U函数
    'entry' => 'ucuser/Index/index',

   'admin_entry' => 'ucuser/Ucuser/index',

    'icon'=>'comments',

    'can_uninstall' => 0
);