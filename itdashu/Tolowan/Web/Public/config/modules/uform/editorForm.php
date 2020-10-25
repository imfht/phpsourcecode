<?php
$config = array(
    'form' => array(
        'action' => '/',
        'method' => 'post',
    ),
    'name' => array(
        'type' => 'field',
        'label' => '表单名称',
        'description' => '这是一个描述',
        'field' => 'string',
        'setting' => array(
            'widget' => 'Text',
        ),
        'validate' => array(
            array(
                'v' => 'StringLength',
                'min' => 4,
                'max' => 40
            )
        ),
        'attributes' => array(),
    ),
    'machine' => array(
        'type' => 'field',
        'label' => '机读名',
        'description' => '只能是小写字母或下划线',
        'field' => 'string',
        'setting' => array(
            'widget' => 'Text',
        ),
        'attributes' => array(),
    ),
    'description' => array(
        'type' => 'field',
        'label' => '表单描述',
        'description' => '可以是表单填写提示等。',
        'field' => 'textLong',
        'setting' => array(
            'widget' => 'Textarea',
        ),
        'attributes' => array(),
    ),
);