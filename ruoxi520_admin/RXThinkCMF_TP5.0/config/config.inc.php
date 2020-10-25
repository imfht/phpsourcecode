<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

//定义域名常量
define('MAIN_URL','http://main.rxthink5.com');
define('SITE_URL','http://www.rxthink5.com');
define('WAP_URL','http://h5.rxthink5.com');
define('API_URL','http://api.rxthink5.com');
define('IMG_URL','http://images.rxthink5.com');
define('ATTACHMENT_PATH', 'C:\xampp\htdocs\RXThink\RXThink5.0_V2.0_DEV\public\uploads');
define('IMG_PATH', ATTACHMENT_PATH."/img");
define('UPLOAD_TEMP_PATH', IMG_PATH . '/temp');

//配置文件
return [
    // 企业全称
    'site_name'     => 'RXThink_TP5.0版V2.0',
    // 企业简称
    'nick_name'     => 'RXThink框架',
    // 数据库链接
    'db_config'     => 'mysql://root:111111@127.0.0.1:3306/rxthink',
    // 数据表前缀
    'db_prefix'     => 'yk_',
    // 数据库编码
    'db_charset'    => 'utf8mb4',
    // 缓存驱动类型及链接
    'cache_config'  =>'redis://:@127.0.0.1:6379/1',
    // 缓存前缀
    'cache_key'     => 'RX5',
    // 上传参数配置
    'upload'        => [
        //上传图片参数配置
        'image_config'  => [
            // 图片后缀限制
            'upload_image_ext'  => 'jpg|png|gif|bmp|jpeg',
            // 最大上传文件大小(默认：10MB)
            'upload_image_size' => 1024*10,
            // 最大上传数量限制(默认：9张)
            'upload_image_max'  => 9,
        ],
        //上传视频参数配置
        'video_config'  => [
            // 视频后缀限制
            'upload_video_ext'  => 'mp4|avi|mov|rmvb|flv',
            // 最大上传文件大小(默认：10MB)
            'upload_video_size' => 1024*10,
            // 最大上传数量限制(默认：3个)
            'upload_video_max'  => 3,
        ],
    ],
];