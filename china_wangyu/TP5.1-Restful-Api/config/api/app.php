<?php
/**
 * Created by User: wene<china_wangyu@aliyun.com> Date: 2019/3/11 Time: 10:33
 */

# app.php 项目配置文件
return [
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => true,
    // 默认输出类型
    'default_return_type'    => 'json',
    // 默认时区
    'default_timezone'       => 'Asia/Shanghai',

    // 异常页面的模板文件
    'exception_tmpl'         => Env::get('think_path') . 'tpl/think_exception.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '\\think\\restful\\exception\\ApiException',
];