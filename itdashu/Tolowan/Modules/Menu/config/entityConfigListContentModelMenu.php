<?php
$settings = array(
    'formId' => 'menuForm',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'menuForm'
    ),
    'id' => array(
        'label' => '机读名',
        'error' => '',
        'userOptions' => array(),
        'description' => '机读名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'settings' => array(
            'required' => true
        ),
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'name' => array(
        'label' => '菜单名',
        'error' => '',
        'userOptions' => array(),
        'description' => '菜单名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'settings' => array(
            'required' => true
        ),
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'description' => array(
        'label' => '描述',
        'error' => '',
        'userOptions' => array(),
        'description' => '菜单描述',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'validate' => array(),
        'settings' => array(
            'required' => true
        ),
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'attach' => array(
        'label' => '其他',
        'error' => '',
        'userOptions' => array(),
        'filter' => array(),
        'description' => '其他内容',
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'settings' => array(
            'required' => false
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control'
        )
    ),
    'settings' => array(
        'title' => '菜单',
        'description' => '菜单添加、编辑、删除',
        'data' => 'm.menu.list',
        'module' => 'menu',
        'save' => 'Modules\\Core\\Library\\OptionsList::save'
    )
);
