<?php
$settings = array(
    'formId' => 'adminRegionEditForm',
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
    'display_title' => array(
        'label' => '显示标题',
        'error' => '',
        'userOptions' => array(),
        'description' => '是否在前台显示区块标题',
        'field' => 'boole',
        'widget' => 'Select',
        'options' => array('不显示','显示'),
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
        'data' => 'm.region.list',
        'module' => 'region',
        'thead' => array(
            'id' => '机读名',
            'name' => '区域名'
        )
    )
);
