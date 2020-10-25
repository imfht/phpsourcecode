<?php
$settings = array(
    'formId' => 'blockAddText',
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
        'filter' => array(
            'striptags'
        ),
        'validate' => array(),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'name' => array(
        'label' => '区块名',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'body' => array(
        'label' => '内容',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'validate' => array(),
        'filter' => array(),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'settings' => array(
        'checkToken' => false,
    )
);
