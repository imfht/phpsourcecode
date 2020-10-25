<?php
$settings = array(
    'imgToLocal' => array(
        'name' => '远程图片本地化',
        'function' => '\Modules\Core\Library\QueueType::imgToLocal',
    ),
    'sendEmail' => array(
        'name' => '发送邮件',
        'callable' => '\Modules\Core\Library\QueueType::sendEmail'
    ),
    'imgThumbnail' => array(
        'name' => '生成图片缩略图',
        'callable' => '\Modules\Core\Library\QueueType::imgThumbnail'
    ),
    'imgThumbnail' => array(
        'name' => '生成图片缩略图',
        'callable' => '\Modules\Core\Library\QueueType::imgThumbnail'
    ),
    'imgWatermarking' => array(
        'name' => '添加图片水印',
        'callable' => '\Modules\Core\Library\QueueType::imgWatermarking'
    ),
);
