<?php

/**
 * 模版缓存清空函数设置
 * @author HumingXu E-mail:huming17@126.com
 */

function build_cache_template() {
    global $_G;
    $cache_template = SITE_ROOT.'./data/template/*.tpl.php';
    //删除目录内所有模板缓存文件
    $is_unix = is_unix();
    if($is_unix==1 && function_exists('shell_exec')){
        $cmd = 'rm -f '.$cache_template;
        shell_exec($cmd);
    }else{
        //递归删除所有.php 文件
        array_map("unlink", glob( $cache_template));
    }
}
?>