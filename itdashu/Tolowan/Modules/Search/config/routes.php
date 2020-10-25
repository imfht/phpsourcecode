<?php
use Core\Config;
$conf = Config::get('config');
$settings = array(
    'searchSubmit' => array(
        'httpMethods' => 'POST',
        'pattern' => '/search_submit',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Index',
            'module' => 'search',
            'namespace' => 'Modules\Search\Controllers',
        ),
    ),
    'search' => array(
        'httpMethods' => 'GET',
        'pattern' => '/search/{type:([a-z]{2,})}_{word:([\x{4e00}-\x{9fa5}^/_a-zA-Z0-9_]{1,9})}/{page:([1-9]{1}[0-9]{0,11})}.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Search',
            'module' => 'search',
            'namespace' => 'Modules\Search\Controllers',
        ),
    ),
    'adminSearchSubmit' => array(
        'httpMethods' => 'POST',
        'pattern' => '/' . ADMIN_PREFIX . '/search_submit',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'search',
            'action' => 'Index',
            'namespace' => 'Modules\Search\Controllers',
        ),
    ),
    'adminSearch' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/search/{type:([a-z]{2,})}_{word:([\x{4e00}-\x{9fa5}^/_a-zA-Z0-9_]{1,9})}/{page:([1-9]{1}[0-9]{0,11})}.html',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'search',
            'action' => 'Search',
            'namespace' => 'Modules\Search\Controllers',
        ),
    ),
);
