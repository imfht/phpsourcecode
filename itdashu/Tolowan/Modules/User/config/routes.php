<?php
$settings = array(
    'user' => array(
        'httpMethods' => 'GET',
        'pattern' => '/user/{id}.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Index',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'userSign' => array(
        'httpMethods' => 'GET',
        'pattern' => '/user_sign',
        'paths' => array(
            'controller' => 'User',
            'action' => 'Sign',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'userCenterIndex' => array(
        'httpMethods' => 'GET',
        'pattern' => '/user_center/index',
        'paths' => array(
            'controller' => 'User',
            'action' => 'index',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'userCenterCropFace' => array(
        'httpMethods' => null,
        'pattern' => '/user_center/crop_face',
        'paths' => array(
            'controller' => 'User',
            'action' => 'cropFace',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'remote' => array(
        'httpMethods' => null,
        'pattern' => '/remote',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Remote',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'login' => array(
        'httpMethods' => null,
        'pattern' => '/login.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Login',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'logout' => array(
        'httpMethods' => 'GET',
        'pattern' => '/logout.html',
        'paths' => array(
            'controller' => 'User',
            'action' => 'Logout',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'password' => array(
        'httpMethods' => null,
        'pattern' => '/password.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Password',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'resetPassword' => array(
        'httpMethods' => null,
        'pattern' => '/reset_password.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'ResetPassword',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'register' => array(
        'httpMethods' => null,
        'pattern' => '/register.html',
        'paths' => array(
            'controller' => 'Index',
            'action' => 'Register',
            'module' => 'user',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'adminUserEditor' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/user_editor/{id}',
        'paths' => array(
            'module' => 'user',
            'controller' => 'Admin',
            'action' => 'Index',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
    'adminUserHandle' => array(
        'httpMethods' => null,
        'pattern' => '/' . ADMIN_PREFIX . '/user_handle',
        'paths' => array(
            'controller' => 'Admin',
            'module' => 'user',
            'action' => 'Handle',
            'namespace' => 'Modules\User\Controllers',
        ),
    ),
);
