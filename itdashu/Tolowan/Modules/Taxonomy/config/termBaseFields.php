<?php
$settings = array(
    'formId' => 'adminTermEditForm',
    'id' => array(
        'primaryKey' => true,
        'label' => 'id'
    ),
    'form' => array(
        'action' => '',
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeEditForm',
    ),
    'contentModel' => array(
        'field' => 'string',
        'widget' => 'Hidden',
        'description' => '',
        'baseField' => true,
        'access' => array(
            'addForm' => false,
            'editForm' => false,
        ),
        'display' => false,
        'error' => '',
        'attributes' => array(),
        'label' => ''
    ),
    'name' => array(
        'field' => 'string',
        'widget' => 'Text',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '术语名',
        'description' => '术语名',
    ),
    'widget' => array(
        'field' => 'string',
        'widget' => 'Text',
        'baseField' => true,
        'access' => array(
            'addForm' => false,
            'editForm' => false,
        ),
        'default' => 10,
        'required' => false,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '权重',
        'description' => '父级术语ID，非必填，默认为10',
    ),
    'keywords' => array(
        'field' => 'string',
        'widget' => 'Text',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => false,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '关键字',
        'description' => '术语列表页关键字',
    ),
    'description' => array(
        'field' => 'textLong',
        'widget' => 'Textarea',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => '',
        'required' => false,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '描述',
        'description' => '术语描述',
    ),
    'attach' => array(
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'label' => '其他',
        'default' => 1,
        'required' => false,
        'attributes' => array(),
        'error' => '',
        'description' => '其他内容',
    ),
    'settings' => array(
        'checkToken' => false,
        'validation' => true,
    ),
);
