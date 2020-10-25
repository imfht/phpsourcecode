<?php
$settings = array(

    'blog' => array(
        'field' => 'string',
        'widget' => 'Text',
        'isLabel' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'minLength' => 1,
        'maxLength' => 11,
        'isTitle' => true,
        'left' => true,
        'number' => 1,
        'addition' => true,
        'label' => '博客地址',
        'description' => '',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'profession' => array(
        'field' => 'string',
        'widget' => 'Text',
        'isLabel' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'minLength' => 1,
        'maxLength' => 11,
        'isTitle' => true,
        'left' => true,
        'number' => 1,
        'addition' => true,
        'label' => '职业',
        'description' => '',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'description' => array(
        'field' => 'string',
        'widget' => 'Text',
        'isLabel' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'minLength' => 1,
        'maxLength' => 11,
        'isTitle' => true,
        'left' => true,
        'number' => 1,
        'addition' => true,
        'label' => '描述',
        'description' => '',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
);