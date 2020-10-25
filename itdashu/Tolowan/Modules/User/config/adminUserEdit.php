<?php
use Core\Config;
$userSettings = Config::get('m.user.config');
$settings = array(
    'formId' => 'adminUserEditForm',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminUserEditForm',
    ),
    'name' => array(
        'field' => 'string',
        'widget' => 'Text',
        'isLabel' => true,
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '用户名',
        'description' => '用户名',
    ),
    'email' => array(
        'field' => 'string',
        'widget' => 'Text',
        'addForm' => false,
        'editForm' => false,
        'default' => null,
        'required' => false,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '用户邮箱',
        'description' => '',
    ),
    'phone' => array(
        'field' => 'string',
        'widget' => 'Text',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => null,
        'required' => false,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '电话',
        'description' => '',
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
        'description' => '',
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
        'description' => '',
    ),
    'active' => array(
        'field' => 'boole',
        'widget' => 'Radios',
        'access' => array(
            'path' => array(
                '/admin/.*',
                '/.*?/admin/.*'
            )
        ),
        'length' => 10,
        'default' => 0,
        'required' => false,
        'baseField' => true,
        'options' => array(
            '未激活', '已激活'
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'description' => '',
        'error' => '',
        'label' => '激活状态'
    ),
    'email_validate' => array(
        'field' => 'boole',
        'widget' => 'Radios',
        'access' => array(
            'path' => array(
                '/admin/.*',
                '/.*?/admin/.*'
            )
        ),
        'length' => 10,
        'default' => 0,
        'required' => false,
        'baseField' => true,
        'options' => array(
            '未验证', '已验证'
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'description' => '',
        'error' => '',
        'label' => '邮箱验证'
    ),
    'phone_validate' => array(
        'field' => 'boole',
        'widget' => 'Radios',
        'access' => array(
            'path' => array(
                '/admin/.*',
                '/.*?/admin/.*'
            )
        ),
        'length' => 10,
        'default' => 0,
        'required' => false,
        'baseField' => true,
        'options' => array(
            '未验证', '已验证'
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'description' => '',
        'error' => '',
        'label' => '电话验证'
    ),
    'roles' => array(
        'field' => 'string',
        'access' => array(
            'path' => array(
                '/admin/.*',
                '/.*?/admin/.*'
            )
        ),
        'model' => '\Modules\User\Entity\Fields\UserFieldRoles',
        'widget' => 'Selects',
        'minLength' => 2,
        'maxLength' => 20,
        'maxNum' => 20,
        'required' => false,
        'addition' => true,
        'default' => array('user'=>'user'),
        'options' => getRolesOptions(),
        'label' => '角色',
        'description' => '',
        'error' => '',
        'attributes' => array(
            'class' => 'form-control',
        ),
    ),
    /*'verCode' => array(
        'label' => '验证码',
        'userOptions' => array(),
        'error' => '',
        'description' => '请你输入你看到的验证码',
        'field' => 'validateCode',
        'widget' => 'ValidateCode',
        'access' => array(
            'expath' => array(
                '/admin/.*',
                '/.*?/admin/.*'
            )
        ),
        'validate' => array(
            array(
                'v' => 'Function',
                'function' => '\Core\Library\ValidateCode::validate',
                'message' => '验证码输入错误',
            ),
        ),
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),*/
    'settings' => array(
        'error' => '注册失败',
        'success' => '注册成功'
    )
);