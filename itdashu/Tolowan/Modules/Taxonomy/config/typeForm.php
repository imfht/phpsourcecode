<?php $settings = array(
    'formId' => 'adminTaxonomyType',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(
        ),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminTaxonomyType',
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
        'description' => '术语类型名称',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(
        ),
        'attributes' => array(
            'class' => 'form-control',
            'data-error' => '必须是邮箱',
        ),
        'required' => true,
    ),
    'description' => array(
        'label' => '描述',
        'error' => '',
        'userOptions' => array(
        ),
        'description' => '菜单描述',
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
        'title' => '术语类型',
        'description' => '术语类型添加、编辑、删除',
        'dataId' => 'modules.taxonomy.type',
        'module' => 'taxonomy',
        'save' => 'Modules\\Core\\Library\\OptionsList::save',
    ),
);
