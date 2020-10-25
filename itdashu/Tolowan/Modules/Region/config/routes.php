<?php
$settings = array(
    'adminRegionBlockSort' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/region_block_sort/{region}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Sort',
            'module' => 'region',
            'namespace' => 'Modules\Region\Controllers',
        ),
    ),
    'adminRegionBlockAddList' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/region_block/{region}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Index',
            'module' => 'region',
            'namespace' => 'Modules\Region\Controllers',
        ),
    ),
    'adminRegionBlockAdd' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/region_block_add/{region}/{contentModel}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Add',
            'module' => 'region',
            'namespace' => 'Modules\Region\Controllers',
        ),
    ),
    'adminRegionBlockEdit' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/region_block_edit/{region}/{contentModel}/{block}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Edit',
            'module' => 'region',
            'namespace' => 'Modules\Region\Controllers',
        ),
    ),
    'adminRegionBlockDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/region_block_delete/{region}_{block}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'region',
            'action' => 'BlockDelete',
            'namespace' => 'Modules\Region\Controllers',
        ),
    ),
);
