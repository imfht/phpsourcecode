<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-02-29 16:57:19
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-04-09 11:39:00
 */

return [
    'imageUploadRelativePath' => "../attachment/" . date('Ym/d', time()) . '/', // 图片默认上传的目录
    'imageUploadSuccessPath' => date('Ym/d', time()) . '/', // 图片上传成功后，路径前缀
    // 图片服务器的域名设置，拼接保存在数据库中的相对地址，可通过web进行展示
    'domain' => '/attachment/',
    'webuploader' => [
        // 后端处理图片的地址，value 是相对的地址
        'uploadUrl' => '/upload/upload/uploadimg',
        // 多文件分隔符
        'delimiter' => ',',
        // 基本配置
        'baseConfig' => [
            'defaultImage' => '/attachment/image/emptyimg.png',
            'disableGlobalDnd' => true,
            'accept' => [
                'title' => 'Images',
                'extensions' => 'gif,jpg,jpeg,bmp,png',
                'mimeTypes' => 'image/*',
            ],
            'pick' => [
                'multiple' => false,
            ],
        ],
    ],
];
