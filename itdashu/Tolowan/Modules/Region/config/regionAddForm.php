<?php
$settings = array(
    'formId' => 'regionAddForm',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'form_regionAddForm',
    ),
    'machine' => array(
        'label' => '机读名',
        'error' => '',
        'userOptions' => array(
            'labelAttributes' => array(
                'class' => array(),
            ),
            'groupAttributes' => array(
                'class' => array(),
                'id' => 'group_name',
            ),
            'widgetBoxAttributes' => array(
                'class' => array(),
            ),
            'helpAttributes' => array(
                'class' => array(),
            ),
        ),
        'description' => '机读名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'name' => array(
        'label' => '菜单名',
        'error' => '',
        'userOptions' => array(),
        'description' => '菜单名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'settings' => array(
        'save' => '\Modules\Region\Library\Common::saveRegion',
    ),
);
