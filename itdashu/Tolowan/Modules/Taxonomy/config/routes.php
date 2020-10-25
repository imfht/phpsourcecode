<?php
use Core\Config;
$conf = Config::get('config');
$settings = array(
    'autoTermJson' => array(
        'httpMethods' => 'GET',
        'pattern' => '/taxonomy/auto_term_json/{key:([a-zA-Z]{2,})}/{value:([a-zA-Z]{2,})}/{name}.json',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'autoTermJson',
            'module' => 'taxonomy',
            'namespace' => 'Modules\Taxonomy\Controllers',
        ),
    ),
    'adminTermList' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/taxonomy/term/{contentModel:([a-z\-_]{2,})}/{page:([1-9]{1}[0-9]{0,9})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'taxonomy',
            'action' => 'Index',
            'namespace' => 'Modules\Taxonomy\Controllers',
        ),
    ),
    'adminTermEditor' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/term_editor/{id:([1-9]{1}[0-9]{0,9})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'taxonomy',
            'action' => 'Editor',
            'namespace' => 'Modules\Taxonomy\Controllers',
        ),
    ),
    'adminTermDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/term_delete/{id}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'taxonomy',
            'action' => 'Delete',
            'namespace' => 'Modules\Taxonomy\Controllers',
        ),
    ),
);
