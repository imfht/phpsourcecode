<?php
$settings = array(
    'formId' => 'blockType-nodeList',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'ajaxSubmit' => 'right_handle',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'blockType-nodeList',
    ),
    'machine' => array(
        'label' => '机读名',
        'error' => '',
        'userOptions' => array(),
        'description' => '机读名',
        'field' => 'string',
        'widget' => 'Text',
        'filter' => array('striptags'),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'name' => array(
        'label' => '区块名',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'filter' => array('striptags'),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'body' => array(
        'label' => '内容',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'validate' => array(),
        'filter' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(
        'save' => '\Modules\Region\Library\Form::saveBlock',
    ),
);
