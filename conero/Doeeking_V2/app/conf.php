<?php
//  2016年9月20日 星期二 自定义配置文件
return [
    'debug_dir' => 'runtime/',   //  调试输出信息
    /*********前端路径设置***********************/
    '_js_' => '/Conero/public/js/',
    '_css_' => '/Conero/public/css/',
    '_img_' => '/Conero/public/img/',
    /**********其他设置***********************/
    '_auto_common_' => ['config','func'],       //自动加载 模块文件
    '_js_complier'  => 'N',                     // js 是否进行压缩       
    '_js_complier_pre'=>'/Conero/public/js-static/',      // 编译前缀 
    /**********相连项目***********************/
    'http'      => 'http://',
];