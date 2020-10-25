<?php
$settings = array(
    'index' => array(
        'httpMethods' => 'GET',
        'pattern' => '/',
        'paths' => array(
            'controller' => 'Install',
            'action' => 'Index',
            'module' => 'install',
            'namespace' => 'Modules\Install\Controllers',
        ),
    ),
    'test' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/install/test',
        'paths' => array(
            'controller' => 'Install',
            'action' => 'test',
            'module' => 'install',
            'namespace' => 'Modules\Install\Controllers',
        ),
    ),
    'one' => array(
        'httpMethods' => null,
        'pattern' => '/install/one',
        'paths' => array(
            'controller' => 'Install',
            'action' => 'one',
            'module' => 'install',
            'namespace' => 'Modules\Install\Controllers',
        ),
    ),
    'two' => array(
        'httpMethods' => null,
        'pattern' => '/install/two',
        'paths' => array(
            'controller' => 'Install',
            'action' => 'two',
            'module' => 'install',
            'namespace' => 'Modules\Install\Controllers',
        ),
    ),
    'end' => array(
        'httpMethods' => null,
        'pattern' => '/install/end',
        'paths' => array(
            'controller' => 'Install',
            'action' => 'end',
            'module' => 'install',
            'namespace' => 'Modules\Install\Controllers',
        ),
    ),
    'notFound' => array(
        'httpMethods' => 'GET',
        'pattern' => '/install/notFound',
        'paths' => array(
            'controller' => 'Install',
            'action' => 'NotFound',
            'module' => 'install',
            'namespace' => 'Modules\Install\Controllers',
        ),
    ),
);
