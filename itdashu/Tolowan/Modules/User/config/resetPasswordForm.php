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
    'code' => array(
        'field' => 'string',
        'widget' => 'Text',
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
        'label' => '验证码',
        'description' => '请输入您手机或电子邮箱收到的验证码',
    ),
    'password' => array(
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
        'label' => '密码',
        'description' => '您的新密码',
    ),
    'confirmPassword' => array(
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
        'label' => '确认密码',
        'description' => '重复输入您的新密码',
    ),
    'settings' => array(
        'save' => '\Modules\User\Library\Common::resetPassword',
    )
);
