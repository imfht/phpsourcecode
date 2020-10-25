<?php
$settings = array(
    'formId' => 'login',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'login',
    ),
    'user' => array(
        'label' => '用户',
        'userOptions' => array(),
        'error' => '',
        'description' => '用户名或者邮箱',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'password' => array(
        'label' => '密码',
        'userOptions' => array(),
        'error' => '',
        'description' => '登陆密码',
        'field' => 'string',
        'widget' => 'Password',
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'settings' => array(
        'save' => '\Modules\User\Library\Common::login',
        'error' => '登陆失败',
        'success' => '登陆成功'
    ),
);
