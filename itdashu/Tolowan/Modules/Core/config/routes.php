<?php
$settings = array(
    'index' => array(
        'httpMethods' => 'GET',
        'pattern' => '/',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Index',
            'module' => 'core',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminFrame' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/frame',
        'paths' => array(
            'controller' => 'Admin',
            'action' => 'Frame',
            'module' => 'core',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminIndex' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/index',
        'paths' => array(
            'module' => 'core',
            'controller' => 'Admin',
            'action' => 'Index',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminCache' => array(
        'httpMethods' => 'GET',
        'pattern' => '/' . ADMIN_PREFIX . '/cache/{handle:([a-z]{2,})}/{type:([a-z]{2,})}',
        'paths' => array(
            'module' => 'core',
            'controller' => 'Admin',
            'action' => 'Cache',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminModules' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/module',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'Core',
            'action' => 'Modules',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminSecurity' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/security',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'Core',
            'action' => 'Security',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminModulesUninstall' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/module/uninstall/{module:([0-9a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'Core',
            'action' => 'ModulesUninstall',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminModulesEnable' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/module/enable/{module:([0-9a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'Core',
            'action' => 'ModulesEnable',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminModulesDisable' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/module/disable/{module:([0-9a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'Core',
            'action' => 'ModulesDisable',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminThemes' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/Themes',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'core',
            'action' => 'Themes',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminThemesEnable' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/themes/enable/{theme:([0-9a-zA-Z]{2,})}/{controller:([0-9a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'core',
            'action' => 'ThemesEnable',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
    'adminThemesUninstall' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/themes/uninstall/{theme:([0-9a-zA-Z]{2,})}',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'core',
            'action' => 'ThemesUninstall',
            'namespace' => 'Modules\Core\Controllers',
        ),
    ),
);
