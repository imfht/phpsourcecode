<?php
return array(
    //文件上传配置
    'FILE_MAX_SIZE' => 3145728,                             //设置文件上传的大小,单位为字节
    'FILE_EXT_TYPE' => array('jpg', 'gif', 'png', 'jpeg'),  //允许上传的文件后缀（留空为不限制）
    'FILE_ROOT_PATH'=> './Uploads/',                        //设置文件上传的根目录
    'FILE_SAVE_PATH'=> '/file/',                            //设置文件上传的子目录 目前子目录是：./Uploads/file/
    'FILE_SAVE_NAME'=> 'max_' . date('YmdHis'),             //设置上传后的文件名
    'FILE_SUB_NAME' => date('Y-m-d'),                       //设置子目录的具体子目录 目前子目录是：./Uploads/file/2016-06-06
    'FILE_AUTO_SUB' => true,                                //自动使用子目录保存上传文件 默认为true 目前：./Uploads/file/2016-06-06/01.png
    'FILE_REPLACE'  => false,                               //同名文件是否被覆盖 默认false
    'FILE_SAVE_EXT' => '',                                  //上传文件的保存后缀，不设置的话使用原文件后缀
    'FILE_MIMES'    => array(),                             //允许上传的文件类型（留空为不限制）
);