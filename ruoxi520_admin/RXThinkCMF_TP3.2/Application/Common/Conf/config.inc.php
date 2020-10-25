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

/**
 * 系统常规配置
 * 
 * @author 牧羊人
 * @date 2018-09-06
 */

//定义域名常量
define('MAIN_URL','http://main.rxthink3.com');
define('SITE_URL','http://www.rxthink3.com');
define('WAP_URL','http://h5.rxthink3.com');
define('API_URL','http://api.rxthink3.com');
define('IMG_URL','http://images.rxthink3.com');
define('ATTACHMENT_PATH', 'C:\xampp\htdocs\RXThink\RXThink3.2_V2.0_DEV\Uploads');
define('IMG_PATH', ATTACHMENT_PATH."/img");
define('UPLOAD_TEMP_PATH', IMG_PATH . '/temp');

return array(
    'SITE_NAME' => 'RXThink企业级框架TP3版',
    'NICK_NAME' => 'RXThink框架',
    'DB_CONFIG' => 'mysql://root:111111@127.0.0.1:3306/rxthink',
    'CACHE_CONFIG'=>'redis://:@127.0.0.1:6379/1',
    //'CACHE_CONFIG'=> 'memcache://:@127.0.0.1:11211',
    'DB_PREFIX' => 'yk_',
    'DB_CHARSET' => 'utf8mb4',
    'UPLOAD' => array(
        'UPLOAD_IMG_EXT' => 'jpg|png|gif|bmp|jpeg',
        'UPLOAD_IMG_SIZE' => 1024*10,//最大上传10MB文件
        'UPLOAD_IMG_NUM' => 9,//最大上传张数
    ),
    'CKEY' => 'RX',//缓存前缀
);

?>