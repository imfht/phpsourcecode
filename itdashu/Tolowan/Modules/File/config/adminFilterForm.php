<?php
use Core\Config;
$settings = array(
    'formId' => 'adminFileFilterForm',
    'formName' => '过滤',
    'layout' => 'inline',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'class' => 'form-inline',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminFileFilterForm',
    ),
    'state' => array(
        'label' => '状态',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(
            0 => '全部',
            1 => '正常',
            2 => '回收站',
        ),
        'required' => true,
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'content_type' => array(
        'label' => '文件类型',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => Config::get('contentType'),
        'required' => true,
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'access' => array(
        'label' => '权限',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(
            0 => '全部',
            1 => '私有',
            2 => '共有',
        ),
        'required' => true,
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'settings' => array(),
);
