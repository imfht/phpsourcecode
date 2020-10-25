<?php
$settings = array(
    'formId' => 'blockAdd',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'blockAdd',
        'ajax-submit' => '#right_handle'
    ),
    'id' => array(
        'label' => '机读名',
        'error' => '',
        'userOptions' => array(),
        'description' => '机读名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'filter' => array('striptags'),
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
    'src' => array(
        'label' => '图片地址',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'fileBox',
        'widget' => 'FileBox',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'settings' => array(
        'checkToken' => false,
    ),
);
