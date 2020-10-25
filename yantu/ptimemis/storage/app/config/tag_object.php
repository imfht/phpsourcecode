<?php return array (
  'base' => 
  array (
    'name' => 'tag_object',
    'comment' => '标签与实体关联表',
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
    'tag_id' => 
    array (
      'name' => 'tag_id',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '标签id',
      'type' => 'number',
      'length' => '11',
      'in_list' => 0,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 1,
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
  ),
);