<?php
$settings = array(
    'formId' => 'userBaseInfo',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'userBaseInfo',
    ),
    'description' => array(
        'field' => 'textLong',
        'widget' => 'Textarea',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => null,
        'wordsmith' => false,
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '个人签名',
        'description' => '',
    ),
    'email' => array(
        'field' => 'email',
        'widget' => 'Email',
        'addForm' => false,
        'editForm' => false,
        'default' => null,
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '用户邮箱',
        'description' => '修改后需要重新认证',
    ),
    'phone' => array(
        'field' => 'string',
        'widget' => 'Text',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => null,
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '电话',
        'description' => '修改后需要重新认证',
    ),
    'sex' => array(
        'field' => 'number',
        'widget' => 'Select',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => null,
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'options' => array(
            '不能鉴定',
            '男娃',
            '女娃'
        ),
        'error' => '',
        'label' => '性别',
        'description' => '',
    ),
    'profession' => array(
        'field' => 'string',
        'widget' => 'Text',
        'access' => array(
            'addForm' => true,
            'editForm' => true,
        ),
        'default' => null,
        'required' => true,
        'baseField' => true,
        'attributes' => array(
            'class' => 'form-control',
        ),
        'error' => '',
        'label' => '职业',
        'description' => '您所从事的职业',
    ),
    'settings' => array(
        'error' => '注册失败',
        'success' => '注册成功'
    )
);