<?php
$settings = array(
    'formId' => 'menuLinkAdd',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'ajaxSubmit' => 'right_handle',
        'id' => 'menuLinkAdd',
        'ajax-submit' => '#right_handle'
    ),
    'id' => array(
        'label' => '机读名',
        'userOptions' => array(),
        'error' => '',
        'description' => '机读名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'name' => array(
        'label' => '菜单名',
        'userOptions' => array(),
        'error' => '',
        'description' => '菜单名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'href' => array(
        'label' => '链接',
        'error' => '',
        'userOptions' => array(),
        'description' => '菜单指向链接',
        'field' => 'string',
        'widget' => 'Text',
        'settings' => array(
            'init' => '\Modules\Menu\Library\Common::hrefInit',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'description' => array(
        'label' => '描述',
        'error' => '',
        'userOptions' => array(),
        'description' => '菜单描述',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'error' => '',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'attach' => array(
        'label' => '其他',
        'error' => '',
        'userOptions' => array(),
        'filter' => array(),
        'description' => '其他内容',
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    'settings' => array(
        'checkToken' => false,
        'save' => '\Modules\Menu\Library\Form::saveLink',
    ),
);
