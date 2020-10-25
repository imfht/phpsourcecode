<?php
$config = array(
    'form' => array(
        'action' => '/',
        'method' => 'post'
    ),
    'sename' => array(
        'type' => 'field',
        'label' => 'select选项',
        'description' => '列表选项，形式如：key:value;key:value',
        'field' => 'textLong',
        'setting' => array(
            'widget' => 'textarea'
        ),
        'attributes' => array()
    )
);