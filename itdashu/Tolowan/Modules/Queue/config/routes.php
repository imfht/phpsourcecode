<?php
$settings = array(
    'queue' => array(
        'httpMethods' => 'GET',
        'pattern' => '/queue/{id:([0-9]{0,11})}',
        'paths' => array(
            'module' => 'queue',
            'controller' => 'Index',
            'action' => 'Index',
            'namespace' => 'Modules\Queue\Controllers',
        ),
    ),
    'adminQueue' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/queue/list_{page:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'module' => 'queue',
            'controller' => 'Admin',
            'action' => 'Index',
            'namespace' => 'Modules\Queue\Controllers',
        ),
    ),
    'adminQueueDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/queue/delete_{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'module' => 'queue',
            'controller' => 'Admin',
            'action' => 'Delete',
            'namespace' => 'Modules\Queue\Controllers',
        ),
    ),
);
