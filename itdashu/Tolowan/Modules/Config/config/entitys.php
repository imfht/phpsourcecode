<?php
$settings = array(
    'config' => array(
        'id' => 'config', //实体类型机读名
        'entityName' => '配置',
        'module' => 'config',
        'label' => 'name', //实体类型阅读名
        'entityModel' => '\Modules\Config\Entity\Config',
        'entityManager' => '\Modules\Config\Entity\ConfigManager',
        'source' => 'node', //实体类型数据库
        'form' => array(
            'default' => 'node.node',
            'handle' => 'node.handle',
            'edit' => 'node.node', //编辑表单
        ),
        'path' => array(
            'list' => array(
                'for' => 'config',
            ),
            'add' => false,
            'delete' => false,
        ),
        'thead' => array(
            'id' => 'ID',
            'label' => '配置名',
            'user' => '描述',
        ),
    ),
    'configList' => array(
        'id' => 'configList', //实体类型机读名
        'name' => '配置列表',
        'module' => 'config',
        'label' => 'name', //实体类型阅读名
        'entityModel' => '\Modules\Config\Entity\ConfigList',
        'entityManager' => '\Modules\Config\Entity\ConfigListManager',
        'source' => 'node', //实体类型数据库
        'form' => array(
            'default' => 'node.node',
            'handle' => 'node.handle',
            'edit' => 'node.node', //编辑表单
        ),
        'path' => array(
            'list' => array(
                'for' => 'config',
            ),
            'add' => false,
            'delete' => false,
        ),
        'thead' => array(
            'id' => 'ID',
            'label' => '内容',
            'user' => '用户',
            'created' => '创建',
            'changed' => '最近更改',
            'state' => '状态',
        ),
        'entity_keys' => array(
            'id' => 'nid',
            'revision' => 'vid',
            'label' => 'title',
            'uuid' => 'uuid',
            'status' => 'status',
            'uid' => 'uid',
        ),
    )
);