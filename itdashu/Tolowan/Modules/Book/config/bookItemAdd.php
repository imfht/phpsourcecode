<?php
$settings = array(
    'formId' => 'bookItemAdd',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'bookItemAdd',
    ),
    'bid' => array(
        'field' => 'number',
        'widget' => 'Chosen',
        'source' => '/book_chosen',
        'valueInit' => '\Modules\Node\Library\Common::idToTitle',
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
        'label' => '书本',
        'description' => '',
    ),
    'nid' => array(
        'field' => 'number',
        'widget' => 'Chosen',
        'source' => '/node_chosen/0',
        'valueInit' => '\Modules\Node\Library\Common::idToTitle',
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
        'label' => '文章',
        'description' => '将该文章加入本书本目录',
    ),
    'pid' => array(
        'field' => 'number',
        'widget' => 'Text',
        'baseField' => true,
        'access' => array(
            'addForm' => false,
            'editForm' => false,
        ),
        'default' => 10,
        'required' => false,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '父项目',
        'description' => '该文章在书本目录中的父节点',
    ),
    'weight' => array(
        'field' => 'number',
        'widget' => 'Text',
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
        'label' => '权重',
        'description' => '该文章在书本目录中的排序权重',
    ),
    'title' => array(
        'field' => 'string',
        'widget' => 'Text',
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
        'label' => '项目标题',
        'description' => '该文章在书本目录中的名称',
    ),
    'settings' => array(
        'checkToken' => false,
        'validation' => true,
        'save' => 'Modules\Book\Library\Common::bookItemSave'
    ),
);
