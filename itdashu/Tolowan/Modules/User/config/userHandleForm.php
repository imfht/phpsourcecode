<?php
$settings = array(
    'formId' => 'adminNodeFilterForm',
    'formName' => '过滤',
    'layout' => 'inline',
    'form' => array(
        'action' => '',
        'method' => 'get',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeFilterForm',
    ),
    'action' => array(
        'label' => '行为',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(
            '彻底删除','标记为草稿','移到回收站','软删除','标记为热点','置顶','精华'
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(),
);
