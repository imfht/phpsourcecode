<?php
$settings = array(
    'comment' => array(
        'id' => 'comment', //实体类型机读名
        'entityName' => '内容',
        'module' => 'comment',
        'entityModel' => '\Modules\Comment\Entity\Comment',
        'entityManager' => '\Modules\Comment\Entity\CommentManager',
        'source' => 'comment', //实体类型数据库
        'filterForm' => 'taxonomy.filterForm',
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
