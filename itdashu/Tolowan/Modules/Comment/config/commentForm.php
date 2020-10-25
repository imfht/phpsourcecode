<?php
$settings = array(
    'formId' => 'comment',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => 'clearfix',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'comment',
    ),
    'nid' => array(
        'label' => '',
        'description' => '',
        'field' => 'number',
        'wordsmiths' => false,
        'error' => '',
        'widget' => 'Hidden',
        'required' => true,
        'validate' => array(),
        'attributes' => array(),
    ),
    'pid' => array(
        'label' => '',
        'description' => '',
        'field' => 'number',
        'wordsmiths' => false,
        'error' => '',
        'value' => 0,
        'widget' => 'Hidden',
        'required' => true,
        'validate' => array(),
        'attributes' => array(),
    ),
    'body' => array(
        'label' => '评论',
        'description' => '发布评论',
        'field' => 'textLong',
        'error' => '',
        'widget' => 'Textarea',
        'required' => true,
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
            'placeholder' => '当你的才华还撑不起你的野心时,那你就应该来评论下'
        ),
    ),
    'settings' => array()
);
