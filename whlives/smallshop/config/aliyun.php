<?php

return [
    /**
     * 阿里云oss配置
     */
    'oss' => [
        'id' => env('ALIYUN_OSS_ID'),//阿里云oss id
        'secret' => env('ALIYUN_OSS_SECRET'),//阿里云oss secret
        'bucket' => env('ALIYUN_OSS_BUCKET'),//bucket名称
        'endpoint' => env('ALIYUN_OSS_ENDPOINT'),//oss所在区域名
        'region_id' => env('ALIYUN_OSS_REGION_ID'),//所在区
    ],
    'sts' => [
        'endpoint' => env('ALIYUN_STS_ENDPOINT'),//sts所在区域名
        'rolearn' => env('ALIYUN_OSS_ROLE_ARN')//角色资源
    ]

];
