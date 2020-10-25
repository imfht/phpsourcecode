<?php
$settings = array(
    'formId' => 'blockAddFullText',
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
        'userOptions' => array(
            'wordsmiths' => true,
        ),
        'description' => '',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'validate' => array(
            'class' => 'form-control',
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'filter' => array('striptags'),
        'required' => true,
    ),
    'body' => array(
        'label' => '内容',
        'error' => '',
        'userOptions' => array(
            'wordsmiths' => true,
        ),
        'description' => '',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(
        'checkToken' => false,
    ),
);
