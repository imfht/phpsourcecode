<?php
$settings = array(
    'validateCode' => array(
        'httpMethods' => 'GET',
        'pattern' => '/validate_code',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'validateCode',
            'module' => 'form',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'cxselect' => array(
        'httpMethods' => 'GET',
        'pattern' => '/cxselect.json',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'cxselect',
            'module' => 'form',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'chosenSource' => array(
        'httpMethods' => 'GET',
        'pattern' => '/chosen_source/{id:([a-z_A-Z]{2,})}',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'chosenSource',
            'module' => 'form',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'adminFormList' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/form',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'form',
            'action' => 'Index',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'adminFormEdit' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/form_edit/{id:([a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'form',
            'action' => 'edit',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'adminFormDelete' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/form_delete/{id:([a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'form',
            'action' => 'delete',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'adminFormFieldSort' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/form_field_sort/{id:([a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'form',
            'action' => 'fieldSort',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'adminFormFieldAdd' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/form_field_add/{id:([a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'form',
            'action' => 'fieldAdd',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
    'adminFormFieldEdit' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/form_field_edit/{form_id:([a-zA-Z]{2,})}/id:([a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'form',
            'action' => 'fieldEdit',
            'namespace' => 'Modules\Form\Controllers',
        ),
    ),
);
