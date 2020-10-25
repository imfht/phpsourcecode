<?php
$settings = array(
    'term' => array(
        'httpMethods' => 'GET',
        'pattern' => '/taxonomy_term/{id:([1-9]{1}[0-9]{0,11})}_{page:([1-9]{1}[0-9]{0,11})}.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Term',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'node' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/node/{id:([1-9]{1}[0-9]{0,11})}.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Index',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodePagination' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/node/{id:([1-9]{1}[0-9]{0,11})}_{page:([1-9]{0,2})}.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Index',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeBrowse' => array(
        'httpMethods' => 'POST',
        'pattern' => '/node_browse/{id:([0-9]{0,11})}',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Browse',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeLove' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/node_love/{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'controller' => 'User',
            'action' => 'Love',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeType' => array(
        'httpMethods' => 'GET',
        'pattern' => '/node_type/{contentModel:([a-z]{2,})}_{page:([1-9]{1}[0-9]{0,11})}.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Type',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeAdd' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/node/add/{contentModel:([a-z]{2,})}',
        'paths' => array(
            'controller' => 'User',
            'action' => 'Add',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeEditor' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/node/editor/{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'controller' => 'User',
            'action' => 'Editor',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeDelete' => array(
        'httpMethods' => array('GET', 'POST'),
        'pattern' => '/node/delete/{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'controller' => 'User',
            'action' => 'Delete',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
    'nodeChosen' => array(
        'httpMethods' => 'GET',
        'pattern' => '/node_chosen/{userlock:([0-1]{1})}',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'chosenSource',
            'module' => 'node',
            'namespace' => 'Modules\Node\Controllers',
        ),
    ),
);
