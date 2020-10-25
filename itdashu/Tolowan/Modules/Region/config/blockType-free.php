<?php
$settings = array(
    'formId' => 'blockAdd',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'ajaxSubmit' => 'right_handle',
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
        'checkToken' => false,
    ),
);
