<?php
$settings = array(
    'formId' => 'adminFormEditForm',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeEditForm',
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
        'label' => '表单机读名',
        'description' => '',
    ),
    'title' => array(
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
        'label' => '表单名',
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
        'label' => '表单描述',
        'description' => '表单描述',
    ),
    'attach' => array(
        'field' => 'kvgroup',
        'widget' => 'Kvgroup',
        'baseField' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'label' => '其他',
        'default' => 1,
        'required' => false,
        'attributes' => array(),
        'error' => '',
        'description' => '其他内容',
    ),
    'settings' => array(
        'save' => '\Modules\Form\Library\AdminForm::userFormEdit',
        'checkToken' => false,
        'validation' => true,
    ),
);
