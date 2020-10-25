<?php
$settings = array(
    'node' => array(
        'id' => 'node',
        'entityName' => '内容',
        'module' => 'node',
        'entityModel' => '\Modules\Node\Entity\Node',
        'entityManager' => '\Modules\Node\Entity\NodeManager',
        'source' => 'node', //实体类型数据库
        'filterForm' => 'node.filterForm',
        'path' => array(
            'adminEntityList' => true,
            'adminEntityEdit' => true,
            'adminEntityAdd' => true,
            'adminEntityDelete' => true,
            'adminEntityHandle'=> true,
            'entity' => false,
            'entityList' => false,
            'entityContentModelList' => false,
            'entityModelFieldList' => false,
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
