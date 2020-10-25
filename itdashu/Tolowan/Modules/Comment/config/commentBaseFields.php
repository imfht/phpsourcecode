<?php
$settings = array(
    'formId' => 'comment',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'class' => 'comment-form',
        'id' => 'comment-form',
    ),
    'id' => array(
        'primaryKey' => true,
        'label' => 'id',
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
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '',
    ),
    'nid' => array(
        'field' => 'string',
        'widget' => 'Hidden',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(),
        'label' => '文章ID',
        'description' => '文章ID',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'pid' => array(
        'field' => 'string',
        'widget' => 'Hidden',
        'baseField' => true,
        'access' => array(
            'addForm' => false,
            'editForm' => false,
        ),
        'default' => 0,
        'required' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '父评论',
        'description' => '父级术语ID，非必填，默认为10',
    ),
    'body' => array(
        'field' => 'textLong',
        'widget' => 'Textarea',
        'baseField' => true,
        'configInit' => ',{customConfig:"http://cdn.itdashu.com/modules/ckeditor/config-base.js"}',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control',
            'placeholder' => '当你的才华还撑不起你的野心时,那你就应该来评论下~~'
        ),
        'label' => '评论',
        'error' => '',
        'description' => '留下您的见解吧~',
    ),
    'settings' => array(
        'checkToken' => false,
        'validation' => true,
    ),
);
