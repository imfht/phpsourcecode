<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 10:54
 */

//全局配置
return [
    //基础设置
    'debug'=>Yuri2::isLocal(), //debug模式
    'show_debug_btn'=>Yuri2::isLocal(), //显示右下角的debug小按钮
    'error_log_lv'=>9, //记录错误的等级
    'turn_off_error_display'=>true, //关闭原版错误提示
    'auto_session_start'=>true,//是否自动开启session
    'doc_check'=>true,//是否开启注释引擎
    'charset'=>'utf-8',//默认字符集，建议保持utf-8
    'log_visitor'=>true,//记录访问者信息到日志
    'log_visitor_local'=>false,//记录127.0.0.1的访问到日志
    'html_pages_dir'=>'htmlPageRes',//存放页面html资源文件的父文件夹名
    //-----------------------------------------------------
    //服务器设置
    'enable_ini_set'=>true,//使下列设置生效
    'timezone'=>'PRC',//时区（PRC表示北京时间）
    'time_limit'=>60,//超时时间  单位s
    'memory_limit'=>1024,//内存限制 单位M
    'max_input_time'=>20,//表单提交最大时间 单位s
    'post_max_size'=>50,//POST提交数据上限 单位M
    'upload_max_filesize'=>50,//文件上传的最大文件上限 单位M
    'ignore_repeated_errors'=>'on',//忽略重复的错误
    'ignore_repeated_source'=>'on',//忽略重复的错误来源
    'xdebug_var_display_max_children'=>256,//xdebug 最多孩子节点数
    'xdebug_var_display_max_data'=>128,//xdebug 最大字节数
    'xdebug_var_display_max_depth'=>16,//xdebug 最大深度
    //-----------------------------------------------------
    //路由设置
    'url_mode'=>2,
    'mask_suffix'=>'jsp',
    'single_module'=>false,//单模块模式
    'default_module'=>'Home',//默认模块名
    'bind_follow_order'=>true,//按顺序绑定路由参数？否则按键值对绑定
    //-----------------------------------------------------
    //控制器设置
    'ajax_format'=>'json',//自动把数组返回值转为json
    //-----------------------------------------------------
    //模板引擎设置
    'tpl_suffix'=>'html',//模板后缀
    'tpl_auto_escape'=>true,//输出模板自动转义
    //-----------------------------------------------------

];