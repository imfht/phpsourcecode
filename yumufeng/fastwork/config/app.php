<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 12:40
 */

use fastwork\facades\Env;

return [
    /**
     * 默认模块
     */
    'default_module' => 'index',
    /**
     * 默认控制器
     */
    'default_controller' => 'Index',
    /**
     * 默认操作
     */
    'default_action' => 'index',
    /**
     * 默认过滤
     */
    'default_filter' => '',
    /**
     * 异常捕获的模板
     */
    'http_exception_template' => Env::get('app_path') . 'exception.php',

];