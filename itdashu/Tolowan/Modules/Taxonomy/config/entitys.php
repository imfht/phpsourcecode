<?php
$settings = array(
    'term' => array(
        'id' => 'term', //实体类型机读名
        'entityName' => '内容',
        'module' => 'taxonomy',
        'entityModel' => '\Modules\Taxonomy\Entity\Term',
        'entityManager' => '\Modules\Taxonomy\Entity\TermManager',
        'source' => 'term', //实体类型数据库
        'storage' => 'MultiTable',
        'filterForm' => 'taxonomy.filterForm',
        'path' => array(
            'adminEntityList' => false,
            'adminEntityEdit' => false,
            'adminEntityAdd' => false,
            'adminEntityDelete' => false,
            'adminEntityHandle'=> false,
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
