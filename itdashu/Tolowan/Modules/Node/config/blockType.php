<?php
$settings = array(
    'nodeList' => array(
        'entity' => 'node.blockType-nodeList',
        'name' => '文章列表',
        'description' => ''
    ),
    'fullText' => array(
        'entity' => 'region.blockType-fullText',
        'name' => 'HTML富文本',
        'description' => 'html文本，允许任何html标签，不得包含任何标签'
    ),
    'image' => array(
        'entity' => 'region.blockType-image',
        'name' => '图片',
        'description' => '包含图片名、描述、链接的图片区块'
    ),
    'free' => array(
        'entity' => 'region.blockType-free',
        'name' => '自由字段',
        'description' => '包含key：value形式的区块，允许灵活的增改删字段'
    ),
);
