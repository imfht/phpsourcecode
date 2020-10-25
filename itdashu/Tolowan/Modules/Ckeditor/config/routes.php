<?php
$settings = array(
    'ckeditorUploade' => array(
        'httpMethods' => null,
        'pattern' => '/ckeditor/upload_image',
        'paths' => array(
            "controller" => 'Index',
            "action" => 'UploadImage',
            'module' => 'ckeditor',
            "namespace" => 'Modules\Ckeditor\Controllers',
        ),
    ),
    'ckeditorBrowseImage' => array(
        'httpMethods' => 'GET',
        'pattern' => '/ckeditor/browse_image',
        'paths' => array(
            "controller" => 'Index',
            "action" => 'Index',
            'module' => 'ckeditor',
            "namespace" => 'Modules\Ckeditor\Controllers',
        ),
    ),
    'ckeditorBrowseImageList' => array(
        'httpMethods' => 'GET',
        'pattern' => '/ckeditor/browse_image_list/{page:([1-9]{1}[0-9]{0,8})}',
        'paths' => array(
            "controller" => 'Index',
            'module' => 'ckeditor',
            "action" => 'BrowseImageList',
            "namespace" => 'Modules\Ckeditor\Controllers',
        ),
    ),
);
