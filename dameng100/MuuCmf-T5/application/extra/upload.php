<?php

//上传配置
return [

    'image' => [

        //文件保存格式
        'savepath'   => ROOT_PATH . 'public' . DS . 'uploads'  . DS . 'picture',
        //最大可上传大小
        'maxsize'   => 2*1024*1024,
        //可上传的文件类型
        'mimetype'  => 'jpg,png,bmp,jpeg,gif',
    ],

    'file' => [
        //文件保存格式
        'savepath'   => ROOT_PATH . 'public' . DS . 'uploads'  . DS . 'file',
        //最大可上传大小
        'maxsize'   => 1024*1024*1024,
        //可上传的文件类型
        'mimetype'  => 'zip,rar,xls,xlsx,doc,docx,pdf,mp3,mp4,mpge,avi',
    ],

    'avatar' => [

        //文件保存格式
        'savepath'   => ROOT_PATH . 'public' . DS . 'uploads'  . DS . 'avatar',
        //最大可上传大小
        'maxsize'   => 2*1024*1024,
        //可上传的文件类型
        'mimetype'  => 'jpg,png,bmp,jpeg,gif',
    ],
    
];
