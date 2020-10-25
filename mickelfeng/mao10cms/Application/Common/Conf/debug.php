<?php
return  array(
    'LOG_RECORD'            =>  false,  // 进行日志记录
    'LOG_EXCEPTION_RECORD'  =>  false,    // 是否记录异常信息日志
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL',  // 允许记录的日志级别
    'DB_FIELDS_CACHE'       =>  true, // 字段缓存信息
    'DB_SQL_LOG'            =>  false, // 记录SQL信息
    'TMPL_CACHE_ON'         =>  false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_STRIP_SPACE'      =>  false,       // 是否去除模板文件里面的html空格与换行
    'SHOW_ERROR_MSG'        =>  false,    // 显示错误信息
    'URL_CASE_INSENSITIVE'  =>  true,  // URL不区分大小写
);