<?php
use Core\Config;

$settings = array(
    'formId' => 'adminUserFilterForm',
    'formName' => '过滤',
    'layout' => 'inline',
    'form' => array(
        'action' => '',
        'method' => 'get',
        'class' => 'form-inline',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeFilterForm',
    ),
    'roles' => array(
        'label' => '角色',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'string',
        'widget' => 'Select',
        'options' => array(),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'state' => array(
        'label' => '状态',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(
            0 => '不限制',
            1 => '待通过',
            2 => '正常',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'active' => array(
        'label' => '激活',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'number',
        'widget' => 'Select',
        'options' => array(
            0 => '不限制',
            1 => '待激活',
            2 => '已激活',
        ),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'validate' => array(),
        'required' => true,
    ),
    'settings' => array(),
);
$userType = Config::get('m.user.entityUserContentModelList');
$output = array();
foreach ($userType as $key => $value) {
    $output[$key] = $value['modelName'];
}
$settings['roles']['options'] = $output;
unset($output);
unset($key);
unset($value);
unset($userType);