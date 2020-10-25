<?php return array (
  'base' => 
  array (
    'name' => 'role_permission',
    'comment' => '角色权限关联表',
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
    'role_id' => 
    array (
      'name' => 'role_id',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '角色id',
      'type' => 'number',
      'length' => '11',
      'in_list' => 0,
      'in_show' => 1,
      'in_store' => 1,
      'in_search' => 0,
      'in_filter' => 1,
      'in_sort' => 1,
    ),
    'permission_id' => 
    array (
      'name' => 'permission_id',
      'null' => 'NO',
      'key' => '',
      'default' => NULL,
      'comment' => '权限id',
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