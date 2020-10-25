<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(
    'URL_MODEL' => 2,
    'HTML_CACHE_ON' => true, 
    'HTML_CACHE_TIME' => 60, 
    'HTML_READ_TYPE' => 0,
    'HTML_FILE_SUFFIX' => '.tpl',
    'HTML_CACHE_RULES' => array(
        'index' => array('{:module}_{:controller}_{:action}_{cid}',-1),
    ),


);