<?php $settings = array(
    'formId' => 'adminRegion',
    'form' => array(
        'method' => 'post',
        'class' => '',
        'accept-charset' => 'utf-8',
        'id' => 'adminRegion',
    ),
    'machine' => array(
        'label' => '机读名',
        'error' => '',
        'userOptions' => array(
        ),
        'description' => '机读名',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'name' => array(
        'label' => '名称',
        'error' => '',
        'userOptions' => array(
        ),
        'description' => '区域名称',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'description' => array(
        'label' => '描述',
        'error' => '',
        'userOptions' => array(
        ),
        'description' => '区域描述',
        'field' => 'textLong',
        'widget' => 'Textarea',
        'validate' => array(
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(
        'title' => '区域列表',
        'description' => '区域添加、编辑、删除',
        'dataId' => 'modules.region.region',
        'module' => 'region',
        'save' => 'Modules\\Core\\Library\\OptionsList::save',
    ),
);
