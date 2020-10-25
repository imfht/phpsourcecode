<?php

return [
    'path' => [
        'title' => '生成图片路径',
        'type'  => 'text',
        'value' => './upload/qrcode'
    ],
    'type' => [
        'title'   => '生成图片类型',
        'type'    => 'radio',
        'options' => [
            'jpg'  => 'jpg',
            'png'  => 'png',
            'gif'  => 'gif',
            'bmp'  => 'bmp',
            'jpeg' => 'jpeg',
        ],
        'value'   => 'jpg'
    ],
];