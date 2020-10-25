<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

// 前端配置文件

$static_domain  = config('static_domain');
$frontend_theme = config('frontend_theme');
$module         = request()->module();

$view_path = ROOT_PATH . SYS_APP_NAMESPACE . SYS_DS_PROS . $module. SYS_DS_PROS . LAYER_VIEW_NAME . SYS_DS_PROS . $frontend_theme . SYS_DS_PROS;

empty($static_domain) ? $static['__STATIC__'] =  SYS_DS_PROS . SYS_STATIC_DIR_NAME . SYS_DS_PROS . $module . SYS_DS_PROS . $frontend_theme :  $static['__STATIC__'] = $static_domain . SYS_DS_PROS . SYS_STATIC_DIR_NAME . SYS_DS_PROS . $module . SYS_DS_PROS . $frontend_theme;

return [
    
    'template' => ['view_path' => $view_path],
    
    /* 模板常量替换配置 */
    'view_replace_str' => $static,
];
