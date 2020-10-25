<?php
$settings = array(
    'node' => array(
        'id' => 'node',
        'label' => 'title', //实体类型阅读名
        'entity' => '\Modules\Node\Entity\Node', //内容类型实类
        'source' => 'node', //实体类型数据库
        'storage' => 'MultiTable',
        'filterForm' => 'node.filterForm',
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
    ),
);
