<?php
$settings = array(
    'adminFileManage' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/file_manage/{page:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            "controller" => 'Admin',
            "action" => 'Index',
            'module' => 'file',
            "namespace" => 'Modules\File\Controllers',
        ),
    ),
    'adminFileDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/file_delete/{id}',
        'paths' => array(
            "controller" => 'Admin',
            'module' => 'file',
            "action" => 'Delete',
            "namespace" => 'Modules\File\Controllers',
        ),
    ),
    'privateFile' => array(
        'httpMethods' => 'GET',
        'pattern' => '/private_file/{id:([1-9]{1}[0-9]{0,})}',
        'paths' => array(
            "controller" => 'Index',
            "action" => 'Index',
            'module' => 'file',
            "namespace" => 'Modules\File\Controllers',
        ),
    ),
    'imagesBoxList' => array(
        'httpMethods' => 'GET',
        'pattern' => '/images_box/list/{page:([1-9]{1}[0-9]{0,})}',
        'paths' => array(
            "controller" => 'Index',
            "action" => 'ImagesBoxList',
            'module' => 'file',
            "namespace" => 'Modules\File\Controllers',
        ),
    ),
    'imagesBoxUpload' => array(
        'httpMethods' => null,
        'pattern' => '/images_box/upload',
        'paths' => array(
            "controller" => 'Index',
            "action" => 'ImagesBoxUpload',
            'module' => 'file',
            "namespace" => 'Modules\File\Controllers',
        ),
    ),
);
