<?php
/***
 * @开源软件: 店滴AI-基于AI的软硬件开源解决方案
 * @官方地址: http://www.wayfirer.com/
 * @版本: 1.0
 * @邮箱: 2192138785@qq.com
 * @作者: Wang Chunsheng
 * @Date: 2020-02-28 22:38:37
 * @LastEditTime: 2020-04-25 02:47:40
 */

return [
    'id' => 'app-backend-tests',
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__.'/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
        ],
    ],
];
