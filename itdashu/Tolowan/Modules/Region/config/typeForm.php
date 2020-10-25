<?php
$config = array(
    'form' => array(
        'action' => '/',
        'method' => 'post',
    ),
    'name' => array(
        'label' => '类型名称',
        'description' => '这是一个描述',
        'field' => 'string',
        'setting' => array(
            'widget' => 'Text',
        ),
        'attributes' => array(),
    ),
    'machine' => array(
        'label' => '机读名',
        'description' => '这是一个描述',
        'field' => 'string',
        'setting' => array(
            'widget' => 'Text',
        ),
        'attributes' => array(),
    ),
    'description' => array(
        'label' => '类型描述',
        'description' => '可以是字段填写提示等。',
        'field' => 'textLong',
        'setting' => array(
            'widget' => 'Textarea',
        ),
        'attributes' => array(),
    ),
);