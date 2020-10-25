<?php
$settings = array(
    'formId' => 'adminTermForm',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminTermForm',
    ),
    'type' => array(
        'label' => '',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Hidden',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'name' => array(
        'label' => '术语名',
        'error' => '',
        'userOptions' => array(),
        'description' => '术语名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'widget' => array(
        'label' => '权重',
        'error' => '',
        'userOptions' => array(),
        'description' => '父级术语ID，非必填，默认为10',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'description' => array(
        'label' => '描述',
        'error' => '',
        'userOptions' => array(),
        'description' => '术语描述',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => false,
    ),
    'attach' => array(
        'label' => '其他',
        'error' => '',
        'userOptions' => array(),
        'filter' => array('attachToString'),
        'description' => '其他内容',
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'settings' => array(
        'save' => 'term',
        'success' => '添加/编辑术语成功',
        'error' => '添加/编辑术语失败',
    ),

);
