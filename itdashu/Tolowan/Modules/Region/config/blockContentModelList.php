<?php
$settings = array(
    'text' => array(
        'fields' => 'region.blockType-text',
        'modelName' => 'txt/纯文本',
        'module' => 'region',
        'description' => '一段纯文本文字，不得包含任何标签'
    ),
    'fullText' => array(
        'fields' => 'region.blockType-fullText',
        'modelName' => 'HTML富文本',
        'module' => 'region',
        'description' => 'html文本，允许任何html标签，不得包含任何标签'
    ),
    'image' => array(
        'fields' => 'region.blockType-image',
        'modelName' => '图片',
        'module' => 'region',
        'description' => '包含图片名、描述、链接的图片区块'
    ),
    'free' => array(
        'fields' => 'region.blockType-free',
        'modelName' => '自由字段',
        'module' => 'region',
        'description' => '包含key：value形式的区块，允许灵活的增改删字段'
    ),
);
