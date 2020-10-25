<?php
$settings = array(
    'formId' => 'blockAdd',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'ajax-submit' => '#right_handle'
    ),
    'region' => array(
        'label' => '所属区块',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'validate' => array(),
        'options' => regionList(),
        'filter' => array('striptags'),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),

    'base' => array(
        'field' => 'group',
        'widget' => 'Group',
        'label' => '基本信息',
        'description' => '',
        'error' => '',
        'access' => array(),
        'attributes' => array(),
        'right' => true,
        'group' => array(
            'path' => array(
                'field' => 'textLong',
                'widget' => 'Textarea',
                'access' => array(
                    'addForm' => false,
                    'editForm' => false,
                    'baseField' => true,
                ),
                'length' => 10,
                'required' => false,
                'description' => '只为指定页面显示该区块，支持正则，每行一个',
                'error' => '',
                'right' => 'true',
                'label' => '显示页面',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'expath' => array(
                'field' => 'textLong',
                'widget' => 'Textarea',
                'access' => array(
                    'addForm' => true,
                    'editForm' => true,
                    'baseField' => true,
                ),
                'length' => 10,
                'required' => false,
                'label' => '隐藏页面',
                'description' => '在指定页面隐藏该区块，支持正则，每行一个',
                'error' => '',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'roles' => array(
                'field' => 'string',
                'widget' => 'Selects',
                'options' => getRolesOptions(),
                'required' => false,
                'label' => '为指定角色显示该区块',
                'description' => '',
                'error' => '',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'exroles' => array(
                'field' => 'string',
                'widget' => 'Selects',
                'options' => getRolesOptions(),
                'required' => false,
                'label' => '为指定角色隐藏该区块',
                'description' => '',
                'error' => '',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'user' => array(
                'field' => 'string',
                'widget' => 'Text',
                'required' => false,
                'label' => '为指定用户显示该区块',
                'description' => '用户id，用英文逗号隔开',
                'error' => '',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'exuser' => array(
                'field' => 'string',
                'widget' => 'Text',
                'required' => false,
                'label' => '为指定用户隐藏该区块',
                'description' => '用户id，用英文逗号隔开',
                'error' => '',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
            'open_page' => array(
                'field' => 'number',
                'widget' => 'Select',
                'options' => array(
                    '禁止单页访问',
                    '允许单页访问',
                    '只允许ajax访问'
                ),
                'required' => false,
                'label' => '通过单页访问',
                'description' => '启用后这个区块将会生成一个页面',
                'error' => '',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            ),
        ),
    ),
    'contentModel' => array(
        'label' => '内容模型',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Hidden',
        'validate' => array(),
        'filter' => array('striptags'),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array()
);