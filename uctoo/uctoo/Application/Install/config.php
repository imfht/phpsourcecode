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
/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return [
    'ORIGINAL_TABLE_PREFIX' => 'uctoo_', //默认表前缀
    /* 模板相关配置 */
    'view_replace_str' => array(
        '__STATIC__' => ROOT_PATH . '/Public/static',
        '__ADDONS__' => ROOT_PATH . '/Public/' . request()->module() . '/Addons',
        '__IMG__' => ROOT_PATH . '/Public/' . request()->module() . '/images',
        '__CSS__' => ROOT_PATH . '/Public/' . request()->module() . '/css',
        '__JS__' => ROOT_PATH . '/Public/' . request()->module() . '/js',
        '__ZUI__'=>ROOT_PATH.'/Public/zui',
        '__NAME__'=>'UCToo',
        '__COMPANY__'=>'深圳优创智投科技有限公司',
        '__WEBSITE__'=>'www.uctoo.cn',
        '__COMPANY_WEBSITE__'=>'www.uctoo.com'
    ),
    /* URL配置 */
    'SESSION_PREFIX' => 'uctoo', //session前缀
    'COOKIE_PREFIX' => 'uctoo_', // Cookie前缀 避免冲突

    //设置session普通存储，安装完成后，系统会配置Session数据库存储——实现用户在线统计
    'SESSION_TYPE'          => '',           //设置session普通存储
];