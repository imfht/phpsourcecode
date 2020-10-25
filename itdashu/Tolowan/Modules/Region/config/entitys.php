<?php
$settings = array(
    'block' => array(
        'id' => 'block', //实体类型机读名
        'name' => '区块',
        'module' => 'region',
        'label' => 'name', //实体类型阅读名
        'entityModel' => '\Modules\Region\Entity\Block',
        'entityManager' => '\Modules\Region\Entity\BlockManager',
        'source' => 'm.region.blockList', //实体类型数据库
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