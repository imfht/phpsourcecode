<?php return array (
  'base' => 
  array (
    'name' => 'permission',
    'comment' => '权限表',
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
    'name' => 
    array (
      'name' => 'name',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '名称',
      'type' => 'string',
      'length' => '20',
      'in_list' => 1,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 1,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
    'controller' => 
    array (
      'name' => 'controller',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '关联控制器',
      'type' => 'string',
      'length' => '20',
      'in_list' => 1,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
    'action' => 
    array (
      'name' => 'action',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '关联动作',
      'type' => 'string',
      'length' => '20',
      'in_list' => 1,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
  ),
);