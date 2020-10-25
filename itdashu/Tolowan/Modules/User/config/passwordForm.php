<?php
$settings = array(
	'formId' => 'changePassword',
	'form' => array(
		'action' => '',
		'method' => 'post',
		'class' => array(),
		'accept-charset' => 'utf-8',
		'role' => 'form',
		'id' => 'changePassword',
	),
    'user' => array(
        'field' => 'string',
        'widget' => 'Password',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => '',
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '账户信息',
        'description' => '您的邮箱、手机号码、昵称中任意一项',
    ),
    'type' => array(
        'field' => 'number',
        'widget' => 'Select',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => '',
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'options' => array(
            1 => '邮箱验证码',
            2 => '手机验证码',
        ),
        'error' => '',
        'label' => '密码',
        'description' => '您的新密码',
    ),
    'settings' => []
);
