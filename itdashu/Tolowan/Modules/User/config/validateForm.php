<?php
$settings = array(
    'formId' => 'user_validate',
    'form' => array(
        'action' => '',
        'method' => 'post',
        'class' => array(),
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'user_validate',
    ),
    'phoneVerCode' => array(
        'label' => '验证码',
        'userOptions' => array(),
        'error' => '',
        'description' => '验证码',
        'field' => 'string',
        'widget' => 'Text',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'settings' => array(
        'save' => '\Modules\User\Library\Common::userValidate',
        'etype' => 'node.page',
        'type' => 'page',
        'key' => 'systemSetting',
        'module' => 'node',
        'entityTableColumns' => array('e.body'),
    ),
);
