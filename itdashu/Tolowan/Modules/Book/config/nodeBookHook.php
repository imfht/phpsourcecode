<?php
$settings = array(
    'field' => 'group',
    'widget' => 'Group',
    'label' => '编入书本',
    'description' => '',
    'error' => '',
    'attributes' => array(),
    'access' => array(
        'path' => array(
            '/admin/.*',
            '/.*?/admin/.*'
        )
    ),
    'right' => true,
    'group' => array(
        'book_id' => array(
            'field' => 'node',
            'widget' => 'Select',
            'valueInit' => '\Modules\Node\Library\Common::idToTitle',
            'access' => array(
                'addForm' => false,
                'editForm' => false,
                'baseField' => true,
            ),
            'contentModel' => 'book',
            'length' => 10,
            'required' => false,
            'description' => '添加该文章到此书本目录',
            'error' => '',
            'right' => 'true',
            'dataType' => 'id',
            'label' => '书本',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ),
    ),
);