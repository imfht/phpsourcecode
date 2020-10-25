<?php return array (
  'base' => 
  array (
    'name' => 'alias',
    'comment' => '别名表',
    'opera' => 
    array (
      'show' => 1,
      'store' => 1,
      'update' => 1,
      'destroy' => 1,
      'search' => 1,
      'filter' => 1,
      'page' => 1,
    ),
  ),
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'null' => 'NO',
      'key' => 'PRI',
      'default' => NULL,
      'comment' => '',
      'type' => 'number',
      'length' => '11',
      'in_list' => 0,
      'in_show' => 1,
      'in_store' => 0,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 0,
    ),
    'object' => 
    array (
      'name' => 'object',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '实体',
      'type' => 'string',
      'length' => '20',
      'in_list' => 1,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
    'object_id' => 
    array (
      'name' => 'object_id',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '实体id',
      'type' => 'number',
      'length' => '11',
      'in_list' => 0,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
    'name' => 
    array (
      'name' => 'name',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '名称',
      'type' => 'string',
      'length' => '200',
      'in_list' => 0,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 1,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
  ),
);