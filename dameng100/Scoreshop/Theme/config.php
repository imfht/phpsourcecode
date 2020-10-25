<?php

$now_theme = cookie('TO_LOOK_THEME','',array('prefix'=>'MUUCMF'));
if(!$now_theme){
    $now_theme=muu_now_theme();
}
if($now_theme!='default'){
    return array(
        /* 模板相关配置 */
        'TMPL_PARSE_STRING' => array(
            '__THEME__'=>__ROOT__.'/Theme/'.$now_theme,
            '__THEME_COMMON_STATIC__'=>__ROOT__.'/Theme/'.$now_theme.'/Common/Static',
            '__THEME_STATIC__'=>__ROOT__.'/Theme/'.$now_theme.'/'.MODULE_NAME.'/Static',
            '__THEME_CSS__'=>__ROOT__.'/Theme/'.$now_theme.'/'.MODULE_NAME.'/Static/css',
            '__THEME_JS__'=>__ROOT__.'/Theme/'.$now_theme.'/'.MODULE_NAME.'/Static/js',
            '__THEME_IMG__'=>__ROOT__.'/Theme/'.$now_theme.'/'.MODULE_NAME.'/Static/images',
            '__THEME_VIEW__'=>__ROOT__.'/Theme/'.$now_theme.'/'.MODULE_NAME.'/View',
            '__THEME_VIEW_PUBLIC__'=>__ROOT__.'/Theme/'.$now_theme.'/'.MODULE_NAME.'/View/Public',
            '__THEME_PUBLIC__'=>__ROOT__.'/Theme/'.$now_theme.'/Public',
        ),
    );
}
