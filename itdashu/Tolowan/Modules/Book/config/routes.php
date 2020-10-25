<?php
$settings = array(
    'adminBookTocSort' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/book/toc_sort/{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Index',
            'module' => 'Book',
            'namespace' => 'Modules\Book\Controllers',
        ),
    ),
    'adminBookTocEdit' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/book/toc_edit/{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Edit',
            'module' => 'Book',
            'namespace' => 'Modules\Book\Controllers',
        ),
    ),
    'adminBookTocDelete' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/book/toc_delete/{id:([1-9]{1}[0-9]{0,11})}',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Delete',
            'module' => 'Book',
            'namespace' => 'Modules\Book\Controllers',
        ),
    ),
    'bookChosen' => array(
        'httpMethods' => 'GET',
        'pattern' => '/book_chosen',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'chosenSource',
            'module' => 'book',
            'namespace' => 'Modules\Book\Controllers',
        ),
    ),
);
