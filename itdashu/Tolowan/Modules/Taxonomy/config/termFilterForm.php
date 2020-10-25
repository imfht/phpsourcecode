<?php
use Core\Config;
$settings = array(
    'formId' => 'adminTermFilterForm',
    'formName' => '过滤',
    'layout' => 'inline',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'class' => 'form-inline',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminTermFilterForm',
    ),
    'name' => array(
        'label' => '术语名',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Text',
        'options' => array(),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'contentModel' => array(
        'label' => '术语类型',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(),
);
$taxonomyList = Config::get('m.taxonomy.entityTermContentModelList');
foreach ($taxonomyList as $key => $value) {
    $settings['contentModel']['options'][$key] = $value['modelName'];
}