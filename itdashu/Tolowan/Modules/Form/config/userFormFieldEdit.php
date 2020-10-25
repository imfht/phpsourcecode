<?php
$settings = array(
    'formId' => 'adminFormFieldEditForm',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminFormFieldEditForm',
        'ajax-submit' => '#right_handle',
        'data-selects' => 'field,widget',
        'data-url' => '/cxselect.json',
        'data-required' => 'true',
        'data-json-name' => 'name',
        'data-json-value' => 'value'
    ),
    'id' => array(
        'field' => 'string',
        'widget' => 'Text',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '字段机读名',
        'description' => '',
    ),
    'label' => array(
        'field' => 'string',
        'widget' => 'Text',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '字段名',
        'description' => '',
    ),
    'description' => array(
        'field' => 'textLong',
        'widget' => 'Textarea',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => '',
        'required' => false,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '字段描述',
        'description' => '字段描述',
    ),
    'field' => array(
        'field' => 'string',
        'widget' => 'Select',
        'baseField' => true,
        'options' => array(),
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control field',
            'data-value' => 'string',
            'data-required' => 'false',
            'data-first-title' => '请选择字段类型'
        ),
        'error' => '',
        'label' => '字段类型',
        'description' => '',
    ),
    'widget' => array(
        'field' => 'string',
        'widget' => 'Select',
        'baseField' => true,
        'options' => array(),
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'attributes' => array(
            'class' => 'form-control widget',
            'data-value' => 'Text',
            'data-required' => 'false',
            'data-first-title' => '请选择控件类型'
        ),
        'error' => '',
        'label' => '字段控件',
        'description' => '',
    ),
    'attach' => array(
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'label' => '附加数据',
        'default' => 1,
        'required' => false,
        'attributes' => array(),
        'error' => '',
        'description' => '例如：class add form',
    ),
    'settings' => array(
        'save' => '\Modules\Form\Library\AdminForm::userFormFieldEdit',
        'checkToken' => false,
        'validation' => true,
    ),
);
global $di;
$di->getShared('assets')
    ->addJs('cxselect', 'http://cdn.itdashu.com/library/cxselect/jquery.cxselect.js', 'footer')
    ->addInlineJs('cxselect-init-userFormFieldEdit', '$(\'#adminFormFieldEditForm\').cxSelect();', 'footer');