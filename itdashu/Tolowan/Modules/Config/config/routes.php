<?php
$settings = array(
    'adminConfig' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/config/list',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Config',
            'module' => 'config',
            'namespace' => 'Modules\Config\Controllers',
        ),
    ),
    'adminConfigEdit' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/config/edit/{contentModel:([a-zA-Z\.\-]{3,60})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'ConfigEdit',
            'module' => 'config',
            'namespace' => 'Modules\Config\Controllers',
        ),
    ),
    'adminConfigList' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/config_list/list/{contentModel:([a-zA-Z\.\-]{3,60})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'ConfigList',
            'module' => 'config',
            'namespace' => 'Modules\Config\Controllers',
        ),
    ),
    'adminConfigListEditor' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/config_list/editor/{contentModel:([a-zA-Z\.\-]{3,60})}/{id:([a-zA-Z\-\_]{2,50})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'ConfigListEditor',
            'module' => 'config',
            'namespace' => 'Modules\Config\Controllers',
        ),
    ),
    'adminConfigListDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/config_list/delete/{contentModel:([a-zA-Z\.\-]{3,60})}/{id:([a-zA-Z\-\_]{1,20})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'ConfigListDelete',
            'module' => 'config',
            'namespace' => 'Modules\Config\Controllers',
        ),
    ),
);
