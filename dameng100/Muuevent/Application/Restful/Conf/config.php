<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */

return array(

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Article,OT\\TagLib\\Think',

    // URL伪静态后缀
	'url_html_suffix'        => 'html|xml|json|jsonp',

    'LOAD_EXT_CONFIG' => 'code' //加载额外的配置文件

);

