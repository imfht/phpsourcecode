<?php
use Core\Config;

$settings = array(
    'formId' => 'adminNodeFilterForm',
    'formName' => '过滤',
    'layout' => 'inline',
    'form' => array(
        'action' => staticUrl(),
        'method' => 'get',
        'class' => 'form-inline',
        'ajax-submit' => '#main',
        'accept-charset' => 'utf-8',
        'role' => 'form',
        'id' => 'adminNodeFilterForm',
    ),
    'type' => array(
        'label' => '文章类型',
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
            1 => '正常',
            2 => '回收站',
            3 => '待审核',
        ),
        'validate' => array(),
        'attributes' => array(
            'class' => 'form-control',
        ),
        'required' => true,
    ),
    'top' => array(
        'label' => '置顶',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'boole',
        'widget' => 'Checkbox',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'essence' => array(
        'label' => '精华',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'boole',
        'widget' => 'Checkbox',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'hot' => array(
        'label' => '热点',
        'error' => '',
        'userOptions' => array(),
        'description' => '',
        'field' => 'boole',
        'widget' => 'Checkbox',
        'validate' => array(),
        'attributes' => array(),
        'required' => true,
    ),
    'settings' => array(),
);
$nodeType = Config::get('m.node.type');
$output = array();
foreach ($nodeType as $key => $value) {
    $output[$key] = $value['name'];
}
$settings['type']['options'] = $output;
unset($output);
unset($key);
unset($value);