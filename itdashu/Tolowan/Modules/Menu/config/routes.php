<?php
use Core\Config;
$conf = Config::get('config');
$settings = array(
    'adminMenuLinkList' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/menu_link/{id}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Index',
            'module' => 'menu',
            'namespace' => 'Modules\Menu\Controllers',
        ),
    ),
    'adminMenuLinkAdd' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/menu_link_add/{id}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'menu',
            'action' => 'LinkAdd',
            'namespace' => 'Modules\Menu\Controllers',
        ),
    ),
    'adminMenuLinkEditor' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/menu_link_editor/{id:([a-z]{2,})}_{link:([a-z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'menu',
            'action' => 'LinkEditor',
            'namespace' => 'Modules\Menu\Controllers',
        ),
    ),
    'adminMenuLinkDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/menu_link_delete/{id:([a-z]{2,})}_{link:([a-z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'menu',
            'action' => 'LinkDelete',
            'namespace' => 'Modules\Menu\Controllers',
        ),
    ),
);
