<?php
$settings = array(
    'user' => array(
        'id' => 'user', //实体类型机读名
        'entityName' => '用户',
        'module' => 'user',
        'entityModel' => '\Modules\User\Entity\User',
        'entityManager' => '\Modules\User\Entity\UserManager',
        'source' => 'user', //实体类型数据库
        'filterForm' => 'user.filterForm',
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
