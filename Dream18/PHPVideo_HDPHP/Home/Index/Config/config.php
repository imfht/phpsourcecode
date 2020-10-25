<?php
if (!defined("HDPHP_PATH"))exit('No direct script access allowed');
return array(
	/********************************模板参数********************************/
    'TPL_CHARSET'                   => 'utf-8',     //字符集
    'TPL_PATH'                      => './Theme/',      //模板目录
    'TPL_STYLE'                     => 'Default/',          //风格
    'TPL_FIX'                       => '.html',     //模版文件扩展名
    'TPL_TAGS'                      => array(),     //模板标签
    'TPL_ERROR'                     => 'error',     //错误信息模板
    'TPL_SUCCESS'                   => 'success',   //正确信息模板
    'TPL_ENGINE'                    => 'HD',        //模板引擎 HD,Smarty
    'TPL_TAG_LEFT'                  => '<',         //左标签
    'TPL_TAG_RIGHT'                 => '>',         //右标签
    'TPL_CACHE_TIME'                => -1,          //模板缓存时间 -1为不缓存 0为永久缓存
);
?>