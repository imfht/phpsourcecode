<?php
$settings = array(
    'title' => array(
        'field' => 'string',
        'widget' => 'Text',
        'isLabel' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'minLength' => 1,
        'isTitle' => true,
        'fullTextSearch' => true,
        'maxLength' => 11,
        'number' => 1,
        'addition' => true,
        'search' => true,
        'label' => '标题',
        'description' => '',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'body' => array(
        'field' => 'textLong',
        'widget' => 'Textarea',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'search' => true,
        'fullTextSearch' => true,
        'minLength' => 1,
        'maxLength' => 11,
        'addition' => true,
        'label' => '内容',
        'description' => '',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
);
