<?php

return [

    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix'         => 'KMeKl',
    // 驱动方式 支持redis memcache memcached
    'type'           => '',
    // 是否自动开启 SESSION
    'auto_start'     => true,
    'secure'         => false,
    'maxLifeTime'    => 60 * 24 * 7, //生存期(分钟)
    'savePath'       => '', //保存路径，不设置则为默认

];