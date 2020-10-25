<?php
$settings = array(
    'node' => array(
        'name' => '内容',
        'widget' => array(
            'Text' => '文本框',
            'Select' => '下拉列表',
            'Tags' => '标签框',
        ),
        'init' => '\Modules\Node\Forms\Field::nodeInit',
        'validate' => array(),
        'filter' => array()
    ),
);